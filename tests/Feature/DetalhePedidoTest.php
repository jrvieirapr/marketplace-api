<?php

namespace Tests\Feature;

use App\Models\DetalhePedido;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DetalhePedidoTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testListarTodosDetalhesPedidos()
    {
        //Criar 5 tipos
        //Salvar Temporario
        DetalhePedido::factory()->count(5)->create();

        // usar metodo GET para verificar o retorno
        $response = $this->getJson('/api/detalhespedidos/');

        //Testar ou verificar saida
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'pedido_id','produto_id','quantidade','preco','total', 'created_at', 'updated_at']
                ]
            ]);
    }


    /**
     * Criar um Tipo
     */
    public function testCriarDetalhesPedidosSucesso()
    {

        // Criar um tipo usando o factory
        $pedido = Pedido::factory()->create();
        $produto = Produto::factory()->create();

        //Criar o objeto
        $data = [
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' =>$this->faker->numberBetween($int1 = 0, $int2 = 99999),
            'preco' => $this->faker->numberBetween($int1 = 0, $int2 = 99999),
            'total' => $this->faker->numberBetween($int1 = 0, $int2 = 99999),
        ];


        // Fazer uma requisição POST
        $response = $this->postJson('/api/detalhespedidos/', $data);

        //dd($response);

        // Verifique se teve um retorno 201 - Criado com Sucesso
        // e se a estrutura do JSON Corresponde
        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'pedido_id','produto_id','quantidade','preco','total', 'created_at', 'updated_at']);
    }

    /**
     * Teste de criação com falhas
     *
     * @return void
     */
    public function testCriacaoProdutoFalha()
    {
        $data = [
            'pedido_id' => "",
            'produto_id' => "",
            'quantidade' =>"",
            'preco' => "",
            'total' => "",
        ];
        // Fazer uma requisição POST
        $response = $this->postJson('/api/detalhespedidos/', $data);

        // Verifique se teve um retorno 422 - Falha no salvamento
        // e se a estrutura do JSON Corresponde
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['id', 'pedido_id','produto_id','quantidade','preco','total', 'created_at', 'updated_at']);
    }

    /**
     * Teste de pesquisa de registro
     *
     * @return void
     */
    public function testPesquisaDetalhesPedidosSucesso()
    {
        // Criar um tipo
        $detalhePedido = DetalhePedido::factory()->create();

        // Fazer pesquisa
        $response = $this->getJson('/api/detalhespedidos/' . $detalhePedido->id);

        // Verificar saida
        $response->assertStatus(200)
            ->assertJson([
                'pedido_id' => $detalhePedido->pedido_id,
                'produto_id' => $detalhePedido->produto_id,
                'quantidade' => $detalhePedido->quantidade,
                'preco' => $detalhePedido->preco,
                'total' => $detalhePedido->total,
            ]);
    }


    /**
     * Teste de pesquisa de registro com falha
     *
     * @return void
     */
    public function testPesquisaProdutoComFalha()
    {
        // Fazer pesquisa com um id inexistente
        $response = $this->getJson('/api/detalhespedidos/999'); // o 999 nao pode existir

        // Veriicar a resposta
        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Produto não encontrado'
            ]);
    }

    /**
     *Teste de upgrade com sucesso
     *
     * @return void
     */
    public function testUpdateDetalhesPedidosucesso()
    {
        // Crie um produto fake
        $detalhePedido = DetalhePedido::factory()->create();

        // Dados para update
        $newData = [
            'nome' => 'Novo nome',
            'descricao' => 'Novo nome',
            'preco' => 3.55,
            'estoque' => 5,
            'tipo_id' => $detalhePedido->tipo->id

        ];

        // Faça uma chamada PUT
        $response = $this->putJson('/api/detalhespedidos/' . $detalhePedido->id, $newData);

        // Verifique a resposta
        $response->assertStatus(200)
            ->assertJson([
                'id' => $detalhePedido->id,
                'nome' => 'Novo nome',
                'descricao' => 'Novo nome',
                'preco' => 3.55,
                'estoque' => 5,
                'tipo_id' => $detalhePedido->tipo->id
            ]);
    }

    /**
     *Teste de upgrade com falhas
     *
     * @return void
     */
    public function testUpdateProdutoComFalhas()
    {
        // Crie um produto fake
        $detalhePedido = DetalhePedido::factory()->create();

        // Dados para update      
        $invalidData = [
            'nome' => 'a',
            'descricao' => 'a',
            'preco' => 'a',
            'estoque' => 'a',
            'tipo_id' => 0

        ];
        // Faça uma chamada PUT
        $response = $this->putJson('/api/detalhespedidos/' . $detalhePedido->id, $invalidData);

        // Verificar se teve um erro 422
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'descricao', 'preco', 'estoque', 'tipo_id']);
    }

    /**
     * Teste update de produto
     *
     * @return void
     */
    public function testUpdateProdutoNaoExistente()
    {

        // Criar um tipo usando o factory
        $pedido = Pedido::factory()->create();


        // Dados para update
        $newData = [ 
            'nome' => 'Novo nome',
            'descricao' => 'Novo nome',
            'preco' => 3.55,
            'estoque' => 5,
            'tipo_id' => $pedido->id

        ];
        // Faça uma chamada PUT
        $response = $this->putJson('/api/detalhespedidos/9999', $newData);

        // Verificar o retorno 404
        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Produto não encontrado'
            ]);
    }


    /**
     * Teste de upgrade com os mesmos nome
     *
     * @return void
     */
    public function testUpdateProdutoMesmoNome()
    {
        // Crie um tipo fake
        $detalhePedido = DetalhePedido::factory()->create();

        // Data para update
        $sameData = [
            'nome' => $detalhePedido->nome,
            'descricao' =>
            $detalhePedido->descricao,
            'preco' =>
            $detalhePedido->preco,
            'estoque' =>
            $detalhePedido->estoque,
            'tipo_id'
            => $detalhePedido->tipo->id,
        ];

        // Faça uma chamada PUT
        $response = $this->putJson('/api/detalhespedidos/' . $detalhePedido->id, $sameData);

        // Verifique a resposta
        $response->assertStatus(200)
            ->assertJson([
                'id' => $detalhePedido->id,
                'nome' => $detalhePedido->nome,
                'descricao' =>
                $detalhePedido->descricao,
                'preco' =>
                $detalhePedido->preco,
                'estoque' =>
                $detalhePedido->estoque,
                'tipo_id'
                => $detalhePedido->tipo->id,
            ]);
    }

    /**
     * Teste de upgrade com o nome duplicado
     *
     * @return void
     */
    public function testUpdateProdutoNomeDuplicado()
    {
        // Crie um tipo fake
        $detalhePedido = DetalhePedido::factory()->create();
        $atualizar = DetalhePedido::factory()->create();

        // Data para update
        $sameData = [
            'nome' => $detalhePedido->nome,
            'descricao' => $detalhePedido->descricao,
            'preco' => $detalhePedido->preco,
            'estoque' =>   $detalhePedido->estoque,
            'tipo_id' => $detalhePedido->tipo->id
        ];

        // Faça uma chamada PUT
        $response = $this->putJson('/api/detalhespedidos/' . $atualizar->id, $sameData);

        // Verifique a resposta
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome']);
    }

    /**
     * Teste de deletar com sucesso
     *
     * @return void
     */
    public function testDeleteProduto()
    {
        // Criar produto fake
        $detalhePedido = DetalhePedido::factory()->create();

        // enviar requisição para Delete
        $response = $this->deleteJson('/api/detalhespedidos/' . $detalhePedido->id);

        // Verifica o Delete
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Produto deletado com sucesso!'
            ]);

        //Verifique se foi deletado do banco
        $this->assertDatabaseMissing('DetalhesPedidos', ['id' => $detalhePedido->id]);
    }

    /**
     * Teste remoção de registro inexistente
     *
     * @return void
     */
    public function testDeleteProdutoNaoExistente()
    {
        // enviar requisição para Delete
        $response = $this->deleteJson('/api/detalhespedidos/999');

        // Verifique a resposta
        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Produto não encontrado!'
            ]);
    }
}

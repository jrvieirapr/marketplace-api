<?php

namespace Tests\Feature;

use App\Models\Produto;
use App\Models\Tipo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProdutoTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testListarTodosProdutos()
    {
        //Criar 5 tipos
        //Salvar Temporario
        Produto::factory()->count(5)->create();

        // usar metodo GET para verificar o retorno
        $response = $this->getJson('/api/produtos');

        //Testar ou verificar saida
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'nome', 'descricao', 'preco', 'estoque', 'created_at', 'updated_at']
                ]
            ]);
    }


    /**
     * Criar um Tipo
     */
    public function testCriarProdutoSucesso()
    {

        // Criar um tipo usando o factory
        $tipo = Tipo::factory()->create();

        //Criar o objeto
        $data = [
            'nome' => "" . $this->faker->word . " " .
                $this->faker->numberBetween($int1 = 0, $int2 = 99999),
            'descricao' => $this->faker->sentence(),
            'preco' => $this->faker->randomFloat(2, 10, 1000),
            'estoque' => $this->faker->numberBetween($int1 = 0, $int2 = 99999),
            'tipo_id' => $tipo->id
        ];


        // Fazer uma requisição POST
        $response = $this->postJson('/api/produtos', $data);

        //dd($response);

        // Verifique se teve um retorno 201 - Criado com Sucesso
        // e se a estrutura do JSON Corresponde
        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'nome', 'descricao', 'preco', 'estoque', 'tipo_id', 'created_at', 'updated_at']);
    }

    /**
     * Teste de criação com falhas
     *
     * @return void
     */
    public function testCriacaoProdutoFalha()
    {
        $data = [
            "nome" => 'a',
            "descricao" => 'a',
            "preco" => '',
            "estoque" => '',
            "tipo_id" => ''
        ];
        // Fazer uma requisição POST
        $response = $this->postJson('/api/produtos', $data);

        // Verifique se teve um retorno 422 - Falha no salvamento
        // e se a estrutura do JSON Corresponde
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'descricao', 'preco', 'estoque', 'tipo_id']);
    }

    /**
     * Teste de pesquisa de registro
     *
     * @return void
     */
    public function testPesquisaProdutoSucesso()
    {
        // Criar um tipo
        $produto = Produto::factory()->create();

        // Fazer pesquisa
        $response = $this->getJson('/api/produtos/' . $produto->id);

        // Verificar saida
        $response->assertStatus(200)
            ->assertJson([
                'id' => $produto->id,
                'nome' => $produto->nome,
                'descricao' => $produto->descricao,
                'preco' => $produto->preco,
                'estoque' => $produto->estoque,
                'tipo_id' => $produto->tipo_id,
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
        $response = $this->getJson('/api/produtos/999'); // o 999 nao pode existir

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
    public function testUpdateProdutoSucesso()
    {
        // Crie um produto fake
        $produto = Produto::factory()->create();

        // Dados para update
        $newData = [
            'nome' => 'Novo nome',
            'descricao' => 'Novo nome',
            'preco' => 3.55,
            'estoque' => 5,
            'tipo_id' => $produto->tipo->id

        ];

        // Faça uma chamada PUT
        $response = $this->putJson('/api/produtos/' . $produto->id, $newData);

        // Verifique a resposta
        $response->assertStatus(200)
            ->assertJson([
                'id' => $produto->id,
                'nome' => 'Novo nome',
                'descricao' => 'Novo nome',
                'preco' => 3.55,
                'estoque' => 5,
                'tipo_id' => $produto->tipo->id
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
        $produto = Produto::factory()->create();

        // Dados para update      
        $invalidData = [
            'nome' => 'a',
            'descricao' => 'a',
            'preco' => 'a',
            'estoque' => 'a',
            'tipo_id' => 0

        ];
        // Faça uma chamada PUT
        $response = $this->putJson('/api/produtos/' . $produto->id, $invalidData);

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
        $tipo = Tipo::factory()->create();


        // Dados para update
        $newData = [ 
            'nome' => 'Novo nome',
            'descricao' => 'Novo nome',
            'preco' => 3.55,
            'estoque' => 5,
            'tipo_id' => $tipo->id

        ];
        // Faça uma chamada PUT
        $response = $this->putJson('/api/produtos/9999', $newData);

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
        $produto = Produto::factory()->create();

        // Data para update
        $sameData = [
            'nome' => $produto->nome,
            'descricao' =>
            $produto->descricao,
            'preco' =>
            $produto->preco,
            'estoque' =>
            $produto->estoque,
            'tipo_id'
            => $produto->tipo->id,
        ];

        // Faça uma chamada PUT
        $response = $this->putJson('/api/produtos/' . $produto->id, $sameData);

        // Verifique a resposta
        $response->assertStatus(200)
            ->assertJson([
                'id' => $produto->id,
                'nome' => $produto->nome,
                'descricao' =>
                $produto->descricao,
                'preco' =>
                $produto->preco,
                'estoque' =>
                $produto->estoque,
                'tipo_id'
                => $produto->tipo->id,
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
        $produto = Produto::factory()->create();
        $atualizar = Produto::factory()->create();

        // Data para update
        $sameData = [
            'nome' => $produto->nome,
            'descricao' => $produto->descricao,
            'preco' => $produto->preco,
            'estoque' =>   $produto->estoque,
            'tipo_id' => $produto->tipo->id
        ];

        // Faça uma chamada PUT
        $response = $this->putJson('/api/produtos/' . $atualizar->id, $sameData);

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
        $produto = Produto::factory()->create();

        // enviar requisição para Delete
        $response = $this->deleteJson('/api/produtos/' . $produto->id);

        // Verifica o Delete
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Produto deletado com sucesso!'
            ]);

        //Verifique se foi deletado do banco
        $this->assertDatabaseMissing('produtos', ['id' => $produto->id]);
    }

    /**
     * Teste remoção de registro inexistente
     *
     * @return void
     */
    public function testDeleteProdutoNaoExistente()
    {
        // enviar requisição para Delete
        $response = $this->deleteJson('/api/produtos/999');

        // Verifique a resposta
        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Produto não encontrado!'
            ]);
    }
}

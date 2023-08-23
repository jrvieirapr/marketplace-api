<?php

namespace Tests\Feature;

use App\Models\Marketplace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MarketplaceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testListarTodosMarketplaces()
    {
        //Criar 5 tipos
        //Salvar Temporario
        Marketplace::factory()->count(5)->create();

        // usar metodo GET para verificar o retorno
        $response = $this->getJson('/api/marketplaces');

        //Testar ou verificar saida
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'nome', 'descricao', 'url', 'created_at', 'updated_at']
                ]
            ]);
    }


    /**
     * Criar um Tipo
     */
    public function testCriarMarketplaceSucesso()
    {

        // Criar um tipo usando o factory
        $tipo = Marketplace::factory()->create();

        //Criar o objeto
        $data = [
            'nome' => "" . $this->faker->word . " " .
                $this->faker->numberBetween($int1 = 0, $int2 = 99999),
            'descricao' => $this->faker->sentence(),
            'url' => "" . $this->faker->word . " " .
                $this->faker->numberBetween($int1 = 0, $int2 = 99999),

        ];


        // Fazer uma requisição POST
        $response = $this->postJson('/api/marketplaces', $data);

        //dd($response);

        // Verifique se teve um retorno 201 - Criado com Sucesso
        // e se a estrutura do JSON Corresponde
        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'nome', 'descricao', 'url', 'created_at', 'updated_at']);
    }

    /**
     * Teste de criação com falhas
     *
     * @return void
     */
    public function testCriacaoMarketplaceFalha()
    {
        $data = [
            "nome" => 'a',
            "descricao" => 'a',
            "url" => '',
        ];
        // Fazer uma requisição POST
        $response = $this->postJson('/api/marketplaces', $data);

        // Verifique se teve um retorno 422 - Falha no salvamento
        // e se a estrutura do JSON Corresponde
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'descricao', 'url']);
    }

    /**
     * Teste de pesquisa de registro
     *
     * @return void
     */
    public function testPesquisaMarketplaceSucesso()
    {
        // Criar um tipo
        $marketplace = Marketplace::factory()->create();

        // Fazer pesquisa
        $response = $this->getJson('/api/marketplaces/' . $marketplace->id);

        // Verificar saida
        $response->assertStatus(200)
            ->assertJson([
                'id' => $marketplace->id,
                'nome' => $marketplace->nome,
                'descricao' => $marketplace->descricao,
                'url' => $marketplace->url,
            ]);
    }


    /**
     * Teste de pesquisa de registro com falha
     *
     * @return void
     */
    public function testPesquisaMarketplaceComFalha()
    {
        // Fazer pesquisa com um id inexistente
        $response = $this->getJson('/api/marketplaces/999'); // o 999 nao pode existir

        // Veriicar a resposta
        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Marketplace não encontrado'
            ]);
    }

    /**
     *Teste de upgrade com sucesso
     *
     * @return void
     */
    public function testUpdateMarketplaceSucesso()
    {
        // Crie um Marketplace fake
        $marketplace = Marketplace::factory()->create();

        // Dados para update
        $newData = [
            'nome' => 'Novo nome',
            'descricao' => 'Novo descricao',
            'url' =>  'Novo nome 2',
        ];

        // Faça uma chamada PUT
        $response = $this->putJson('/api/marketplaces/' . $marketplace->id, $newData);

        // Verifique a resposta
        $response->assertStatus(200)
            ->assertJson([
                'id' => $marketplace->id,
                'nome' => 'Novo nome',
                'descricao' => 'Novo descricao',
                'url' => 'Novo nome 2',            ]);
    }

    /**
     *Teste de upgrade com falhas
     *
     * @return void
     */
    public function testUpdateMarketplaceComFalhas()
    {
        // Crie um Marketplace fake
        $marketplace = Marketplace::factory()->create();

        // Dados para update      
        $invalidData = [
            'nome' => 'a',
            'descricao' => 'a',
            'url' => 'a',

        ];
        // Faça uma chamada PUT
        $response = $this->putJson('/api/marketplaces/' . $marketplace->id, $invalidData);

        // Verificar se teve um erro 422
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'descricao', 'url']);
    }

    /**
     * Teste update de Marketplace
     *
     * @return void
     */
    public function testUpdateMarketplaceNaoExistente()
    {

        // Criar um tipo usando o factory
        $tipo = Marketplace::factory()->create();


        // Dados para update
        $newData = [
            'nome' => 'Novo nome',
            'descricao' => 'Novo descricao',
            'url' =>'Novo url 2',

        ];
        // Faça uma chamada PUT
        $response = $this->putJson('/api/marketplaces/9999', $newData);

        // Verificar o retorno 404
        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Marketplace não encontrado'
            ]);
    }


    /**
     * Teste de upgrade com os mesmos nome
     *
     * @return void
     */
    public function testUpdateMarketplaceMesmoNome()
    {
        // Crie um tipo fake
        $marketplace = Marketplace::factory()->create();

        // Data para update
        $sameData = [
            'nome' => $marketplace->nome,
            'descricao' =>
            $marketplace->descricao,
            'url' =>
            $marketplace->url,
        ];

        // Faça uma chamada PUT
        $response = $this->putJson('/api/marketplaces/' . $marketplace->id, $sameData);

        // Verifique a resposta
        $response->assertStatus(200)
            ->assertJson([
                'id' => $marketplace->id,
                'nome' => $marketplace->nome,
                'url' =>
                $marketplace->url,
            ]);
    }

    /**
     * Teste de upgrade com o nome duplicado
     *
     * @return void
     */
    public function testUpdateMarketplaceNomeDuplicado()
    {
        // Crie um tipo fake
        $marketplace = Marketplace::factory()->create();
        $atualizar = Marketplace::factory()->create();

        // Data para update
        $sameData = [
            'nome' => $marketplace->nome,
            'descricao' => $marketplace->descricao,
            'url' => $marketplace->url,

        ];

        // Faça uma chamada PUT
        $response = $this->putJson('/api/marketplaces/' . $atualizar->id, $sameData);

        // Verifique a resposta
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome']);
    }

    /**
     * Teste de deletar com sucesso
     *
     * @return void
     */
    public function testDeleteMarketplace()
    {
        // Criar Marketplace fake
        $marketplace = Marketplace::factory()->create();

        // enviar requisição para Delete
        $response = $this->deleteJson('/api/marketplaces/' . $marketplace->id);

        // Verifica o Delete
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Marketplace deletado com sucesso!'
            ]);

        //Verifique se foi deletado do banco
        $this->assertDatabaseMissing('marketplaces', ['id' => $marketplace->id]);
    }

    /**
     * Teste remoção de registro inexistente
     *
     * @return void
     */
    public function testDeleteMarketplaceNaoExistente()
    {
        // enviar requisição para Delete
        $response = $this->deleteJson('/api/marketplaces/999');

        // Verifique a resposta
        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Marketplace não encontrado!'
            ]);
    }
}

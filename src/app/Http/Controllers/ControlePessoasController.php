<?php

namespace App\Http\Controllers;

use App\Events\Event;
use App\Helpers\Helper;
use App\Models\ControlePessoas;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class ControlePessoasController extends Controller
{

    private $amqprabbit;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Realiza a criação de uma pessoa
     *
     * @param Request $request
     * @return Response
     */
    public function createPessoa(Request $request) {
        $validatedData = Validator::make($request->only(['nome', 'sexo', 'peso', 'altura']), [
            'nome' => 'required|string|unique:controle_pessoas',
            'sexo' => 'required|string|min:1|max:1',
            'peso' => 'required',
            'altura' => 'required',
        ],
        [
            'nome.required' => 'Ops! Informe um nome',
            'nome.string' => 'Somente é permitido letras',
            'nome.unique' => 'Ops! Este nome já está cadastrado.',
            'sexo.required' => 'Ops! Informe um sexo',
            'sexo.string' => 'Somente é permitido letras',
            'sexo.min' => 'Somente é permitido no mínimo 1 caractere',
            'sexo.max' => 'Somente é permitido no máximo 1 caractere',
            'peso.required' => 'Ops! Informe o peso',
            'altura.required' => 'Ops! Informe a altura'
        ]);

        if ($validatedData->fails()) {
            return response()->json(['status' => 501, 'mensagem' => $validatedData->errors()->first()], 501);
        }

        try {

            $pessoa = new ControlePessoas();
            $pessoa->id = preg_replace("/\D+/", "", Uuid::uuid4()->toString()); // Deixar somente os números do UUID4
            $pessoa->nome = ucfirst($request->nome);
            $pessoa->sexo = Str::upper($request->sexo);
            $pessoa->peso = floatval($request->peso);
            $pessoa->altura = floatval($request->altura);
            $pessoa->imc = round(floatval($request->peso / pow($request->altura, 2)), 2);
            $pessoa->save();

            // Evento de Criação
            event(new \App\Events\ControlePessoasEvent("Cadastro da pessoa {$request->nome}"));

            return response()->json([
                'status' => 201,
                'acao' => 'criação',
                'resultado' => 'sucesso'
            ], 201);

            // Cadastro da Pessoa
            $this->amqprabbit->sendMessage("Cadastro da pessoa {$pessoa->nome}");


        } catch(Exception $e) {
            return response()->json([
                'status' => 500,
                'acao' => 'criacao',
                'resultado' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Realiza a atualização de uma pessoa
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function editarPessoa(Request $request, $id) {

        $validatedData = Validator::make($request->only(['nome', 'sexo', 'peso', 'altura']), [
            'nome' => 'required|string',
            'sexo' => 'required|string|min:1|max:1',
            'peso' => 'required',
            'altura' => 'required',
        ],
        [
            'nome.required' => 'Ops! Informe um nome',
            'nome.string' => 'Somente é permitido letras',
            'sexo.required' => 'Ops! Informe um sexo',
            'sexo.string' => 'Somente é permitido letras',
            'sexo.min' => 'Somente é permitido no mínimo 1 caractere',
            'sexo.max' => 'Somente é permitido no máximo 1 caractere',
            'peso.required' => 'Ops! Informe o peso',
            'altura.required' => 'Ops! Informe a altura'
        ]);

        if ($validatedData->fails()) {
            return response()->json(['status' => 501, 'mensagem' => $validatedData->errors()->first()], 501);
        }

        try {

            if(Helper::ignoreColunaUnique(ControlePessoas::class, 'nome',
                $request->nome, 'id', $id))
            {

                ControlePessoas::where('id', $id)
                ->first()->update(
                    [
                        'nome' => ucfirst($request->nome),
                        'sexo' => Str::upper($request->sexo),
                        'peso' => floatval($request->peso),
                        'altura' => floatval($request->altura),
                        'imc' => round(floatval($request->peso / pow($request->altura, 2)), 2)
                    ]
                );

                // Evento de Edição
                event(new \App\Events\ControlePessoasEvent("Edição da pessoa {$request->nome}"));

                return response()->json([
                    'status' => 201,
                    'acao' => 'editar',
                    'resultado' => 'sucesso'
                ], 201);

            } else {
                return response()->json(
                    [
                        'status' => 501,
                        'mensagem' => 'Nome já cadastrado!'
                    ]
                , 501);
            }

        } catch(Exception $e) {

            return response()->json([
                'status' => 500,
                'acao' => 'editar',
                'resultado' => $e->getMessage()
            ], 500);

        }

    }

    /**
     * Retornar todas as pessoas cadastradas
     *
     * @return Response
     */
    public function pessoas() {
        try {

            // Quantidade de Pessoas
            event(new \App\Events\ControlePessoasEvent('Foram Listadas ('.ControlePessoas::count().') Pessoas'));

            return response()->json([
                'status' => 200,
                'acao' => 'buscarPessoas',
                'resultado' => ControlePessoas::all()
            ]);

        } catch(Exception $e) {
            return response()->json([
                'status' => 500,
                'acao' => 'buscarPessoas',
                'resultado' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Realiza a buscar de uma pessoa especifica
     *
     * @param int $id
     * @return Response
     */
    public function pessoa($id) {

        $validatedData = Validator::make(['id' => $id], [
            'id' => 'required|integer'
        ],
        [
            'id.required' => 'Ops! Informe o id',
            'id.integer' => 'Informe somente números'
        ]);

        if ($validatedData->fails()) {
            return response()->json(['status' => 501, 'mensagem' => $validatedData->errors()->first()], 501);
        }

        try {

            $pessoa = ControlePessoas::where('id', $id)->get();

            // Listagem de 1 Pessoa
            event(new \App\Events\ControlePessoasEvent("Listagem da Pessoa {$pessoa->nome}"));

            return response()->json([
                'status' => 200,
                'acao' => 'buscar',
                'resultado' => $pessoa
            ], 200);

        } catch(Exception $e) {
            return response()->json([
                'status' => 500,
                'acao' => 'buscar',
                'resultado' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Excluir uma pessoa especificas
     *
     * @param string $id
     * @return Response
     */
    public function excluirPessoa($id) {

        $validatedData = Validator::make(['id' => $id], [
            'id' => 'required|integer'
        ],
        [
            'id.required' => 'Ops! Informe o id',
            'id.integer' => 'Informe somente números'
        ]);

        if ($validatedData->fails()) {
            return response()->json(['status' => 501, 'mensagem' => $validatedData->errors()->first()], 501);
        }

        try {

            $pessoa = ControlePessoas::where('id', $id)->delete();

            // Evento de Edição
            event(new \App\Events\ControlePessoasEvent("Exclusão da pessoa {$pessoa->nome}"));

            return response()->json([
                'status' => 200,
                'acao' => 'excluir',
                'resultado' => 'Exclusão realizada com sucesso!'
            ], 200);

        } catch(Exception $e) {
            return response()->json([
                'status' => 500,
                'acao' => 'excluir',
                'resultado' => $e->getMessage()
            ], 500);
        }
    }
}

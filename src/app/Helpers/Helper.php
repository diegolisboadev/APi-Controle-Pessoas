<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class Helper
{
    public function __construct() {}


    /**
     * Retornar uma resposta à consulta com o token
     */
    public static function repostaComToken(string $token) {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => env('JWT_TTL')
        ], 200);
    }

    /**
     * Ignora o campo unique a atualização de algum registro
     * com o atributo unique na coluna
     *
     * @param Model $model
     * @param string $coluna
     * @param string $registro
     * @param string $id
     * @param string $idItem
     *
     * @return bool $ignoraId
     */
    public static function ignoreColunaUnique($model, $coluna, $registroAlterado, $id, $idItem) {
        $ignoraId = $model::where([
            [$coluna, '=', $registroAlterado],
            ['id', '<>', intval($idItem)]
        ])->count();

        //$ig = $model::where('id', '<>', (int) $idItem)->get();

        return ($ignoraId != 0) ? false : true;
    }
}

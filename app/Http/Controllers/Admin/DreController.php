<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BalancoPatrimonial;
use App\Models\Dres;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Brick\Math\BigDecimal;

use Money\Money;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Currency;
class DreController extends Controller
{

    public function store(Request $request)
    {
        $dados = json_decode($request->input('dados'));
        $type = $request->input('type');

        $periodos = collect($dados)->pluck('items')->flatten(1)->pluck('periodo')->unique();
        $typeCat = $dados[0]->list_values->type ?? null;

        if ($periodos->isEmpty()) {
            return response()->json(['errors' => ['Ano não informado.']]);
        }

        // Normaliza os períodos para comparação (ex: jan/2019 => jan2019)
        $normalizarPeriodo = fn($p) => strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $p));

        $periodosNormalizados = $periodos->map($normalizarPeriodo)->unique()->values()->toArray();

        // Verifica se já existem registros normalizando também no SQL
        $placeholders = implode(',', array_fill(0, count($periodosNormalizados), '?'));

        $existe = Dres::whereRaw(
            "LOWER(REPLACE(REPLACE(REPLACE(periodo, '/', ''), ' ', ''), '-', '')) IN ($placeholders)",
            $periodosNormalizados
        )->exists();

        if ($existe && $type !== 'success') {
            return response()->json([
                'errors' => ["Já existem dados salvos para os períodos selecionados. Deseja continuar mesmo assim?"]
            ]);
        }
        // Normaliza o typeCat para garantir correspondência
        $typeCatNormalizado = strtolower(trim($typeCat));


        // Apaga tudo antes de inserir os novos
        Dres::where('type', $typeCatNormalizado)
            ->whereIn('periodo', $periodosNormalizados)
            ->delete();

        $mapaMeses = [
            'jan' => 0,
            'fev' => 1,
            'mar' => 2,
            'abr' => 3,
            'mai' => 4,
            'jun' => 5,
            'jul' => 6,
            'ago' => 7,
            'set' => 8,
            'out' => 9,
            'nov' => 10,
            'dez' => 11,
        ];

        foreach ($dados as $item) {
            foreach ($item->items as $tabela => $dados_r) {

                $valorOriginal = $dados_r->valor;
                $semPontos = str_replace('.', '', $valorOriginal);
                $tamanho = strlen($semPontos);

                if ($tamanho > 2) {
                    $valorCorrigido = substr($semPontos, 0, $tamanho - 2) . '.' . substr($semPontos, -2);
                } else {
                    $valorCorrigido = '0.' . str_pad($semPontos, 2, '0', STR_PAD_LEFT);
                }

                $valorFloat = (float) $valorCorrigido;

                if (fmod($valorFloat, 1) === 0.0) {
                    $valorFormatado = number_format($valorFloat, 0, '', '.');
                } else {
                    $valorFormatado = number_format($valorFloat, 2, ',', '.');
                }

                $typePeriodo = strtolower($item->list_values->type); // mensal, trimestral, anual
                $categoriaLimpa = preg_replace(['/"""/', '/\s+/', '/(^"|"$)/'], ['', ' ', ''], $dados_r->categoria);

                $mesTexto = strtolower(substr($dados_r->periodo ?? '', 0, 3));
                $mesNumero = $mapaMeses[$mesTexto] ?? 0;

                $periodo = $dados_r->periodo ?? '';

                $periodoNormalizado = $this->normalizarTexto($periodo);

                $registro = new Dres();
                $registro->type = $typePeriodo;
                $registro->categoria = $categoriaLimpa;
                $registro->month = $mesNumero;
                if ($typePeriodo === 'mensal') {
                    $registro->year = preg_replace('/.*?(\d{4})$/', '$1', $periodo);
                    $registro->periodo = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $periodo));
                } elseif ($typePeriodo === 'trimestral') {
                    if (preg_match('/(\d{4})/', $periodo, $matches)) {
                        $registro->year = (int) $matches[1];
                    } else {
                        $registro->year = null; // ou trate como necessário
                    }
                    $registro->periodo = $dados_r->periodo;
                } elseif ($typePeriodo === 'anual') {
                    $registro->year = $dados_r->periodo;
                    $registro->periodo = $dados_r->periodo;
                }

                $registro->periodo_normalizado = $periodoNormalizado;
                $registro->valor = $valorFormatado;
                $registro->stats = 1;
                $registro->save();
            }
        }

        return response()->json(['success' => true, 'message' => 'BP criado com sucesso!'], 200);
    }

    public function normalizarTexto($texto)
    {
        $texto = iconv('UTF-8', 'ASCII//TRANSLIT', $texto); // remove acentos
        $texto = strtolower($texto);                        // minúsculas
        $texto = trim($texto);                              // remove espaços
        return preg_replace('/\s+/', ' ', $texto);          // normaliza espaços múltiplos
    }

    function normalizarCategoria($texto)
    {
        // Remove acentos
        $texto = iconv('UTF-8', 'ASCII//TRANSLIT', $texto);
        // Transforma em lowercase
        $texto = strtolower($texto);
        // Remove espaços extras
        return trim($texto);
    }
}

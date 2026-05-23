<?php
require(__DIR__.'/../../config.php');
require_login();
require_once($CFG->libdir.'/adminlib.php');

$context = context_system::instance();
require_capability('moodle/site:viewreports', $context);

$PAGE->set_url(new moodle_url('/report/iareport/index.php'));
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->set_title('Relatório IA');
$PAGE->set_heading('🎭 Portal da Cultura com IA');

$apikey = get_config('report_iareport', 'apikey');

$prompts = [
    'filmes' => [
        'label' => '🎬 Filmes mais premiados no Oscar',
        'prompt' => 'Liste 8 filmes mais premiados na história do Oscar.',
        'schema' => ['filme', 'ano', 'numero_de_oscars', 'categoria_principal']
    ],
    'livros' => [
        'label' => '📚 Livros mais vendidos da história',
        'prompt' => 'Liste 8 livros mais vendidos da história da literatura mundial.',
        'schema' => ['livro', 'autor', 'ano_publicacao', 'copias_vendidas']
    ],
    'musicas' => [
        'label' => '🎵 Músicas mais tocadas no Spotify',
        'prompt' => 'Liste 8 músicas mais tocadas de todos os tempos no Spotify.',
        'schema' => ['musica', 'artista', 'ano_lancamento', 'reproducoes']
    ],
    'albuns' => [
        'label' => '💿 Álbuns mais vendidos da história',
        'prompt' => 'Liste 8 álbuns mais vendidos da história da música mundial.',
        'schema' => ['album', 'artista', 'ano_lancamento', 'copias_vendidas']
    ],
    'artes' => [
        'label' => '🎨 Obras de arte mais famosas do mundo',
        'prompt' => 'Liste 8 obras de arte mais famosas do mundo.',
        'schema' => ['obra', 'artista', 'ano', 'museu_ou_localizacao']
    ],
];

$resultado = null;
$erro = null;
$colunas = [];
$fonte = '';
$prompt_selecionado = optional_param('prompt', '', PARAM_ALPHANUMEXT);

$cache = cache::make('report_iareport', 'iareport_cache');
$cache_key = 'prompt_' . $prompt_selecionado;

if ($prompt_selecionado && isset($prompts[$prompt_selecionado])) {

    if (empty($apikey)) {
        $erro = '❌ Chave da API não configurada. Acesse Administração do Site > Relatórios > Relatório IA para configurar.';
    } else {
        $cached = $cache->get($cache_key);

        if ($cached) {
            $resultado = $cached['dados'];
            $colunas = $cached['colunas'];
            $fonte = '⚡ Resultado carregado do cache!';
        } else {
            $item = $prompts[$prompt_selecionado];
            $prompt_final = $item['prompt'] .
                " Retorne APENAS um JSON válido, sem texto adicional, sem markdown, no formato: " .
                "{\"dados\": [{\"" . implode("\": \"...\", \"", $item['schema']) . "\": \"...\"}]}";

            $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apikey;
            $data = json_encode(['contents' => [['parts' => [['text' => $prompt_final]]]]]);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            $response = curl_exec($ch);
            curl_close($ch);

            $raw = json_decode($response, true);
            $texto = $raw['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$texto) {
                $erro = '❌ Não foi possível recuperar as informações da IA. Tente novamente.';
            } else {
                $texto = preg_replace('/```json|```/', '', $texto);
                $json = json_decode(trim($texto), true);
                if (!$json || !isset($json['dados'])) {
                    $erro = '❌ A IA retornou um formato inválido. Tente novamente.';
                } else {
                    $resultado = $json['dados'];
                    $colunas = $item['schema'];
                    $cache->set($cache_key, ['dados' => $resultado, 'colunas' => $colunas]);
                    $fonte = '🌐 Resultado carregado da IA!';
                }
            }
        }
    }
}

echo $OUTPUT->header();
echo '<p>Escolha um tema abaixo para consultar a IA e visualizar os dados em tabela:</p>';

echo '<div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px;">';
foreach ($prompts as $key => $item) {
    $url_btn = new moodle_url('/report/iareport/index.php', ['prompt' => $key]);
    $ativo = ($prompt_selecionado === $key) ? 'background:#0066cc; color:#fff;' : 'background:#eee; color:#333;';
    echo '<a href="' . $url_btn . '" style="padding:10px 16px; border-radius:6px; text-decoration:none; font-weight:bold; ' . $ativo . '">' . $item['label'] . '</a>';
}
echo '</div>';

if ($fonte) {
    echo '<p style="color:gray; font-size:0.9em;">' . $fonte . '</p>';
}

if ($erro) {
    echo '<div style="color:red; font-weight:bold; padding:10px; border:1px solid red; border-radius:6px; margin-bottom:20px;">' . $erro . '</div>';
}

if ($resultado) {
    echo '<h3>' . $prompts[$prompt_selecionado]['label'] . '</h3>';
    $table = new html_table();
    $table->head = array_map(fn($c) => ucfirst(str_replace('_', ' ', $c)), $colunas);
    foreach ($resultado as $row) {
        $line = [];
        foreach ($colunas as $col) {
            $line[] = $row[$col] ?? '-';
        }
        $table->data[] = $line;
    }
    echo html_writer::table($table);
}

echo $OUTPUT->footer();
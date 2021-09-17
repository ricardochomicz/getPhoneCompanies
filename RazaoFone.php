<?php
function entre($str, $param, $fim) {
    $posicao = strpos($str, $param);
    $inicio=substr($str, $posicao+strlen($param));
    $posicao = strpos($inicio, $fim);
    $inicio=substr($inicio, 0, $posicao);
    return $inicio;
}

function buscaTelefone($busca) {
    $url = "https://www.google.com.br/search?q=".urlencode($busca);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    $headers = array();
    $headers[] = 'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $output = curl_exec($ch);
    return $output;
}

if(count($argv) != 3) {
    echo "Comando inválido, utilize da seguinte maneira:\n";
    echo "> php ".$argv[0]." contas.txt \"cidade\" \n";
    exit();
}

$contas = file($argv[1]);
$cidade = $argv[2];
$total = count($contas);
$i = 0;

foreach($contas as $linha) {
    $linha = trim($linha);
    $aConta = explode(";", $linha);
    $razao = trim($aConta[1]);

    echo "[".($i+1)." de $total] Razão: $razao ";

    $textoBusca = 'TELEFONE '.$razao;

    if(isset($aConta[3])){
        $textoBusca .= " ".trim($aConta[3]);
    }

    $htmlBuscaTelefone = buscaTelefone($textoBusca);

    if (strpos($htmlBuscaTelefone , 'has_phone:phone') === false) {
        echo " => NÃO POSSUI TELEFONE!\n";
    } else {
        $telefone = entre($htmlBuscaTelefone,"Ligar para ","role=\"link\">");
        echo " => POSSUI TELEFONE: $telefone\n";
        file_put_contents("INFOS.txt", $aConta[0].";".$razao.";".$cidade.";".$telefone.PHP_EOL, FILE_APPEND);
    }

    sleep(3);
    $i++;
}
<?php

$string['modulename'] = 'Reconhecimento Facial';
$string['pluginname'] = 'Reconhecimento Facial';
$string['facial:view'] = 'Ver Reconhecimento Facial';
$string['facial:manage'] = 'Gerenciar Reconhecimento Facial';
$string['settings'] = 'Configurar Reconhecimento Facial';
$string['start_cam'] = 'Clique aqui para iniciar';

$string['error_imageminvalida'] = '
<p>Imagem recebida é inválida. Tente capturar novamente!</p>
<p>Se o problema persistir tente:</p>
<ul>
    <li>Primeiro tente reiniciar a Página;</li>
    <li>Tente reiniciar o Navegador;</li>
    <li><strong>Se for MAC</strong>: <a target="_blank" href="http://osxdaily.com/2013/12/27/fix-there-is-no-connected-camera-error-mac/">Siga o tutorial deste link</a>;</li>
    <li><strong>Se for Windows</strong>: Reinicie ele.</li>
</ul>';
$string['error_naodetectado'] = 'Não foi possvel detectar algo nesta captura! Não capture em locais escuros ou estar muito longe da webcam!';
$string['error_nenhumacara'] = 'Imagem capturada não possui nenhuma informação!';
$string['error_nenhumapessoa'] = 'Parece que não é uma pessoa na foto. Foi reconhecido a sequencia: <br>{$a}';
$string['error_sequencia'] = 'Foi reconhecido a sequencia: <br>{$a}';
$string['error_nofacecapture'] = 'Não foi detectado uma faces nesta captura. Não capture em locais escuros ou estar muito longe da webcam!';
$string['error_maisfaces'] = 'Foi detectado {$a} faces. Deve haver apenas uma pessoa na captura!';
$string['error_baixaconfianca'] = 'O nível de confiaça de que há um rosto na captura é de {$a}%. Não capture em locais escuros ou estar muito longe da webcam!';
$string['error_boaconfianca'] = 'O nível de confiaça de que há um rosto na captura é de {$a}%.';
$string['error_baixasimilariedade'] = 'O nível de similaridade com a primeira captura foi de {$a}%.<br> Não capture em locais escuros ou estar muito longe da webcam!';
$string['error_altaimilariedade'] = 'O nível de similaridade com a primeira captura ({$a->capturaPrincipal}) foi de {$a->similarityText}%';
$string['error_servidor'] = 'Erro de servidor ao processar esta solicitação!';

<?php
require_once "../config/banco.php";

header('Content-Type: application/json');

if (isset($_POST['id'])) {
    $id_func = $_POST['id'];


    $mes_atual = date('Y-m-01');
    $q_meta = "SELECT * FROM tabela_metas WHERE funcionario_meta = '$id_func' ORDER BY mes_meta DESC LIMIT 5";
    $busca_meta = $banco->query($q_meta);
    $metas = [];
    while ($reg = $busca_meta->fetch_assoc()) {
        $metas[] = $reg;
    }


    $q_vendas = "SELECT v.data_venda, p.nome_produto, vp.qtd_vendido, (vp.qtd_vendido * p.vlr_unitario) as valor_venda
                 FROM tabela_vendas v
                 INNER JOIN tabela_vendas_produtos vp ON v.id = vp.id_venda
                 INNER JOIN tabela_produtos p ON vp.id_produto = p.id
                 WHERE v.funcionario_vendas = '$id_func'
                 ORDER BY v.data_venda DESC LIMIT 10";
    $busca_vendas = $banco->query($q_vendas);
    $vendas = [];
    while ($reg = $busca_vendas->fetch_assoc()) {
        $vendas[] = $reg;
    }

    // Calcular total geral de vendas do funcionário
    $q_total = "SELECT SUM(vp.qtd_vendido * p.vlr_unitario) as total_geral
                FROM tabela_vendas v
                INNER JOIN tabela_vendas_produtos vp ON v.id = vp.id_venda
                INNER JOIN tabela_produtos p ON vp.id_produto = p.id
                WHERE v.funcionario_vendas = '$id_func'";
    $busca_total = $banco->query($q_total);
    $total_geral = $busca_total->fetch_object()->total_geral ?? 0;

    echo json_encode(['metas' => $metas, 'vendas' => $vendas, 'total_geral' => $total_geral]);
}

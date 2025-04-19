SELECT
    raiz.nome_area_mte AS area_mte_raiz,
    pa.nome_pessoa_alvo,
    s.nome_servico
FROM servicos s
JOIN areas_mte a ON s.id_area_mte = a.id_chave_area_mte
JOIN pessoas_alvo pa ON s.id_pessoa_alvo = pa.id_chave_pessoa_alvo
JOIN areas_mte raiz ON (
    -- Subindo na hierarquia até o filho direto de 'MTE'
    a.id_chave_area_mte = raiz.id_chave_area_mte OR
    a.pai = raiz.id_chave_area_mte OR
    (SELECT pai FROM areas_mte WHERE id_chave_area_mte = a.pai) = raiz.id_chave_area_mte OR
    (SELECT pai FROM areas_mte WHERE id_chave_area_mte = (SELECT pai FROM areas_mte WHERE id_chave_area_mte = a.pai)) = raiz.id_chave_area_mte
)
WHERE EXISTS (
    SELECT 1 FROM areas_mte mte WHERE mte.nome_area_mte = 'MTE' AND raiz.pai = mte.id_chave_area_mte
)
ORDER BY
    FIELD(raiz.nome_area_mte, 'SE', 'SEMP', 'SIT', 'SRT', 'SPT'),
    FIELD(pa.nome_pessoa_alvo, 'trabalhadores', 'empregadores', 'entidades'),
    s.nome_servico;


document.addEventListener("DOMContentLoaded", () => {
    const busca = document.getElementById("busca");
    const filtroTurma = document.getElementById("filtroTurma");
    const linhas = document.querySelectorAll("#tabelaMatriculas tbody tr");

    function filtrarTabela() {
        const termoBusca = busca.value.toLowerCase();
        const turmaSelecionada = filtroTurma.value.toLowerCase();

        linhas.forEach(linha => {
            const textoLinha = linha.textContent.toLowerCase();
            const turmaLinha = linha.cells[2].textContent.toLowerCase();

            const combinaBusca = textoLinha.includes(termoBusca);
            const combinaTurma = turmaSelecionada === "" || turmaLinha === turmaSelecionada;

            linha.style.display = (combinaBusca && combinaTurma) ? "" : "none";
        });
    }

    busca.addEventListener("keyup", filtrarTabela);
    filtroTurma.addEventListener("change", filtrarTabela);
});

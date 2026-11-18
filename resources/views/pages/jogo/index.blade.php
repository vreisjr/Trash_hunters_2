<x-layouts::app :title="'Jogo do Trashinho'">
    @vite(['resources/css/jogo.css', 'resources/js/jogo.js'])

    <div class="trashinho">
        <header class="placar">
            <div class="pontos">
                <small>Pontos</small>
                <strong id="pontos">0</strong>
            </div>
            <div class="estagio-box">
                <span id="estagio">Filhote</span>
                <div class="barra" aria-hidden="true"><i id="prog"></i></div>
            </div>
            <div id="vidas" class="vidas" aria-label="Vidas"></div>
        </header>

        <div class="palco">
            <canvas id="game" width="360" height="560" aria-label="Jogo do Trashinho"></canvas>

            <div id="overlay" class="overlay">
                <h1 id="titulo">Trashinho tá com fome</h1>
                <p id="texto">Alimente o mascote com lixo reciclável. Evite pilhas e baterias!</p>
                <div class="legenda">
                    <div class="sim"><span>🍾 📰 🥫</span>Reciclável: +10 pontos</div>
                    <div class="nao"><span>🔋</span>Pilha: perde 1 vida</div>
                </div>
                <p class="dica">Segure ← → (ou A e D) ou arraste na tela</p>
                <p id="recorde"></p>
                <button id="btn" type="button">Jogar</button>
            </div>
        </div>
    </div>
</x-layouts::app>
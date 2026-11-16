const canvas = document.getElementById('game');

if (canvas) {
  const ctx = canvas.getContext('2d');
  const $ = (id) => document.getElementById(id);
  const W = canvas.width, H = canvas.height;

  const ESTAGIOS = [
    { min: 0,   nome: 'Filhote', cor: '#7BD389', tam: 40 },
    { min: 100, nome: 'Jovem',   cor: '#5CC8A1', tam: 50 },
    { min: 250, nome: 'Adulto',  cor: '#4FB3D9', tam: 60 },
    { min: 500, nome: 'Mestre',  cor: '#F2C14E', tam: 70 },
  ];
  const LIXOS = ['🥫', '🍌', '📦', '🧴', '📰', '🍾'];
  const PERIGO = '🔋';

  let estado, rodando = false, ultimo = 0, alvoX = W / 2;
  let moverPorTeclado = () => {};
  const recorde = () => Number(localStorage.getItem('trashinho:recorde') || 0);

  const novo = () => ({ x: W / 2, pontos: 0, vidas: 3, itens: [], tempo: 0, spawn: 0, comendo: 0, dano: 0 });
  const estagio = () => [...ESTAGIOS].reverse().find((e) => estado.pontos >= e.min);

  function spawn() {
    const perigo = Math.random() < 0.22;
    estado.itens.push({
      x: 20 + Math.random() * (W - 40), y: -20,
      v: 110 + Math.random() * 60 + estado.pontos * 0.15,
      e: perigo ? PERIGO : LIXOS[Math.floor(Math.random() * LIXOS.length)],
      perigo,
    });
  }

  function atualizar(dt) {
    const s = estado, est = estagio();
    s.tempo += dt;
    moverPorTeclado(dt);
    s.x += (alvoX - s.x) * Math.min(1, dt * 12);
    s.x = Math.max(est.tam, Math.min(W - est.tam, s.x));
    s.spawn -= dt;
    if (s.spawn <= 0) { spawn(); s.spawn = Math.max(0.35, 0.9 - s.pontos / 1000); }
    s.comendo = Math.max(0, s.comendo - dt);
    s.dano = Math.max(0, s.dano - dt);

    const boca = { x: s.x, y: H - 30 - est.tam * 1.6 };
    s.itens = s.itens.filter((it) => {
      it.y += it.v * dt;
      if (Math.hypot(it.x - boca.x, it.y - boca.y) < est.tam * 0.8) {
        if (it.perigo) { s.vidas--; s.dano = 0.4; } else { s.pontos += 10; s.comendo = 0.25; }
        hud();
        return false;
      }
      return it.y <= H + 20;
    });
    if (s.vidas <= 0) fim();
  }

  function desenhar() {
    ctx.clearRect(0, 0, W, H);
    ctx.fillStyle = '#0C2429';
    ctx.fillRect(0, H - 30, W, 30);
    ctx.font = '28px serif'; ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
    estado.itens.forEach((it) => ctx.fillText(it.e, it.x, it.y));
    mascote();
  }

  function mascote() {
    const s = estado, est = estagio();
    const pulo = Math.sin(s.tempo * 6) * 2;
    const r = est.tam;
    ctx.save();
    ctx.translate(s.x, H - 30 + pulo);
    ctx.fillStyle = s.dano > 0 ? '#E4572E' : est.cor;
    ctx.beginPath();
    ctx.ellipse(0, -r * 0.8, r, r * 0.8, 0, 0, Math.PI * 2);
    ctx.fill();
    ctx.fillStyle = '#fff';
    [-1, 1].forEach((d) => { ctx.beginPath(); ctx.arc(d * r * 0.4, -r * 1.05, r * 0.2, 0, Math.PI * 2); ctx.fill(); });
    ctx.fillStyle = '#12343B';
    [-1, 1].forEach((d) => { ctx.beginPath(); ctx.arc(d * r * 0.4, -r * 1.02, r * 0.09, 0, Math.PI * 2); ctx.fill(); });
    ctx.beginPath();
    if (s.comendo > 0) ctx.ellipse(0, -r * 0.6, r * 0.3, r * 0.22, 0, 0, Math.PI * 2);
    else ctx.arc(0, -r * 0.7, r * 0.3, 0.1 * Math.PI, 0.9 * Math.PI);
    ctx.fill();
    if (est.nome === 'Mestre') { ctx.font = `${r * 0.6}px serif`; ctx.fillText('👑', 0, -r * 1.75); }
    ctx.restore();
  }

  function hud() {
    $('pontos').textContent = estado.pontos;
    $('estagio').textContent = estagio().nome;
    $('vidas').textContent = '❤️'.repeat(Math.max(0, estado.vidas)) || '💔';
  }

  function loop(t) {
    if (!rodando) return;
    const dt = Math.min(0.05, (t - ultimo) / 1000);
    ultimo = t;
    atualizar(dt);
    if (rodando) { desenhar(); requestAnimationFrame(loop); }
  }

  function iniciar() {
    estado = novo(); alvoX = W / 2; hud();
    $('overlay').hidden = true;
    rodando = true; ultimo = performance.now();
    requestAnimationFrame(loop);
  }

  function fim() {
    rodando = false;
    const novoRecorde = estado.pontos > recorde();
    if (novoRecorde) localStorage.setItem('trashinho:recorde', estado.pontos);
    $('overlay').querySelector('h1').textContent = `Fim de jogo: ${estado.pontos} pontos`;
    $('overlay').querySelector('p').textContent = `Trashinho chegou a ${estagio().nome}.`;
    $('recorde').textContent = `Recorde: ${recorde()}${novoRecorde ? ' (novo!)' : ''}`;
    $('btn').textContent = 'Jogar de novo';
    $('overlay').hidden = false;
  }

  // Teclado: segure a seta (ou A/D) para o mascote andar de forma contínua
  const teclas = new Set();
  const MOVER = ['ArrowLeft', 'ArrowRight', 'a', 'A', 'd', 'D'];
  addEventListener('keydown', (e) => {
    if (!MOVER.includes(e.key)) return;
    e.preventDefault();
    teclas.add(e.key);
  });
  addEventListener('keyup', (e) => teclas.delete(e.key));
  addEventListener('blur', () => teclas.clear());
  moverPorTeclado = (dt) => {
    const esq = teclas.has('ArrowLeft') || teclas.has('a') || teclas.has('A');
    const dir = teclas.has('ArrowRight') || teclas.has('d') || teclas.has('D');
    alvoX += ((dir ? 1 : 0) - (esq ? 1 : 0)) * 420 * dt;
    alvoX = Math.max(0, Math.min(W, alvoX));
  };

  // Mouse/toque: só move enquanto estiver segurando e arrastando
  let arrastando = false;
  const posX = (e) => {
    const r = canvas.getBoundingClientRect();
    return ((e.clientX - r.left) / r.width) * W;
  };
  canvas.addEventListener('pointerdown', (e) => {
    arrastando = true;
    canvas.setPointerCapture(e.pointerId);
    alvoX = posX(e);
  });
  canvas.addEventListener('pointermove', (e) => { if (arrastando) alvoX = posX(e); });
  canvas.addEventListener('pointerup', () => { arrastando = false; });
  canvas.addEventListener('pointercancel', () => { arrastando = false; });
  $('btn').addEventListener('click', iniciar);
  $('recorde').textContent = recorde() ? `Recorde: ${recorde()}` : '';
}
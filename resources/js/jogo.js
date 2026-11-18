const canvas = document.getElementById('game');

if (canvas) {
  const ctx = canvas.getContext('2d');
  const $ = (id) => document.getElementById(id);
  const W = 360, H = 560, CHAO = 30;

  // nitidez em telas de alta densidade
  const dpr = Math.min(window.devicePixelRatio || 1, 2);
  canvas.width = W * dpr; canvas.height = H * dpr;
  ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

  const ESTAGIOS = [
    { min: 0,   nome: 'Filhote', cor: '#7BD389', escura: '#3E9A5A', tam: 40 },
    { min: 100, nome: 'Jovem',   cor: '#5CC8A1', escura: '#2F9B7A', tam: 50 },
    { min: 250, nome: 'Adulto',  cor: '#4FB3D9', escura: '#2C85AA', tam: 60 },
    { min: 500, nome: 'Mestre',  cor: '#F2C14E', escura: '#C99522', tam: 70 },
  ];
  const LIXOS = ['🥫', '🍌', '📦', '🧴', '📰', '🍾'];
  const PERIGO = '🔋';
  const CORACAO = '<svg viewBox="0 0 24 24"><path d="M12 21s-8-5.2-8-11a4.6 4.6 0 0 1 8-3 4.6 4.6 0 0 1 8 3c0 5.8-8 11-8 11z"/></svg>';

  let estado, rodando = false, ultimo = 0, alvoX = W / 2;
  let moverPorTeclado = () => {};
  const recorde = () => Number(localStorage.getItem('trashinho:recorde') || 0);

  // poeira de fundo (fixa, só desliza)
  const fundo = Array.from({ length: 28 }, () => ({ x: Math.random() * W, y: Math.random() * H, r: 1 + Math.random() * 1.5, v: 8 + Math.random() * 14 }));

  const novo = () => ({ x: W / 2, pontos: 0, vidas: 3, itens: [], tempo: 0, spawn: 0, mastigar: 0, engolir: 0, dano: 0, particulas: [], popups: [], aviso: null });
  const idxEstagio = () => ESTAGIOS.reduce((a, e, i) => (estado.pontos >= e.min ? i : a), 0);
  const estagio = () => ESTAGIOS[idxEstagio()];

  function spawn() {
    const perigo = Math.random() < 0.22;
    estado.itens.push({
      x: 20 + Math.random() * (W - 40), y: -20,
      v: 110 + Math.random() * 60 + estado.pontos * 0.15,
      e: perigo ? PERIGO : LIXOS[Math.floor(Math.random() * LIXOS.length)],
      perigo, rot: Math.random() * 0.6 - 0.3, vr: Math.random() * 2 - 1,
    });
  }

  function brilho(x, y, cor, n) {
    for (let i = 0; i < n; i++) {
      const a = Math.random() * Math.PI * 2, v = 60 + Math.random() * 120;
      estado.particulas.push({ x, y, vx: Math.cos(a) * v, vy: Math.sin(a) * v - 60, vida: 0.5, cor });
    }
  }

  function atualizar(dt) {
    const s = estado, antes = idxEstagio(), est = estagio();
    s.tempo += dt;
    moverPorTeclado(dt);
    s.x += (alvoX - s.x) * Math.min(1, dt * 12);
    s.x = Math.max(est.tam, Math.min(W - est.tam, s.x));
    s.spawn -= dt;
    if (s.spawn <= 0) { spawn(); s.spawn = Math.max(0.35, 0.9 - s.pontos / 1000); }
    s.mastigar = Math.max(0, s.mastigar - dt);
    s.engolir = Math.max(0, s.engolir - dt);
    s.dano = Math.max(0, s.dano - dt);
    fundo.forEach((p) => { p.y += p.v * dt; if (p.y > H) { p.y = -4; p.x = Math.random() * W; } });

    const bocaY = H - CHAO - est.tam * 0.62;
    s.itens = s.itens.filter((it) => {
      if (it.modo === 'entrando') {                       // indo para dentro da boca
        it.t = Math.min(1, it.t + dt / 0.28);
        if (it.t >= 1) {                                   // engoliu
          s.pontos += 10; s.engolir = 0.15;
          brilho(s.x, bocaY, '#F2C14E', 10);
          s.popups.push({ x: s.x, y: bocaY - est.tam * 1.0, t: 0.8, txt: '+10', cor: '#F2C14E' });
          hud();
          return false;
        }
        return true;
      }
      if (it.modo === 'rejeitado') {                       // pilha quicando para longe
        it.vida -= dt; it.vy += 700 * dt; it.x += it.vx * dt; it.y += it.vy * dt; it.rot += it.vr * 6 * dt;
        return it.vida > 0;
      }
      it.y += it.v * dt; it.rot += it.vr * dt;
      // toca o corpo do mascote (elipse da cabeça)
      const nx = (it.x - s.x) / (est.tam * 1.1);
      const ny = (it.y - (H - CHAO - est.tam * 0.8)) / (est.tam * 0.8 * 1.15);
      if (nx * nx + ny * ny < 1) {
        if (it.perigo) {
          s.vidas--; s.dano = 0.5;
          brilho(it.x, it.y, '#EF5F6B', 14);
          s.popups.push({ x: it.x, y: it.y - 10, t: 0.8, txt: '−1 ❤', cor: '#EF5F6B' });
          Object.assign(it, { modo: 'rejeitado', vx: (it.x >= s.x ? 1 : -1) * (130 + Math.random() * 80), vy: -280, vida: 0.8 });
          hud();
        } else {
          s.mastigar = 0.28 + 0.7;                         // viagem até a boca + mastigada
          Object.assign(it, { modo: 'entrando', t: 0, dx: it.x - s.x, dy: it.y - bocaY });
        }
        return true;
      }
      return it.y <= H + 20;
    });

    if (idxEstagio() > antes) s.aviso = { txt: `Evoluiu: ${estagio().nome}!`, t: 1.4 };
    s.particulas = s.particulas.filter((p) => { p.vida -= dt; p.vy += 300 * dt; p.x += p.vx * dt; p.y += p.vy * dt; return p.vida > 0; });
    s.popups = s.popups.filter((p) => { p.t -= dt; p.y -= 50 * dt; return p.t > 0; });
    if (s.aviso && (s.aviso.t -= dt) <= 0) s.aviso = null;
    if (s.vidas <= 0) fim();
  }

  function cenario() {
    const g = ctx.createLinearGradient(0, 0, 0, H);
    g.addColorStop(0, '#15403A'); g.addColorStop(1, '#0F2E29');
    ctx.fillStyle = g; ctx.fillRect(0, 0, W, H);
    ctx.fillStyle = 'rgba(255,255,255,.09)';
    fundo.forEach((p) => { ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2); ctx.fill(); });
    // chão com grama
    ctx.fillStyle = '#2B2118'; ctx.fillRect(0, H - CHAO, W, CHAO);
    ctx.fillStyle = '#4FA86A'; ctx.fillRect(0, H - CHAO, W, 7);
    for (let x = 0; x <= W; x += 18) { ctx.beginPath(); ctx.arc(x + 9, H - CHAO + 7, 9, 0, Math.PI); ctx.fill(); }
  }

  function item(it) {
    ctx.save();
    let px = it.x, py = it.y, esc = 1;
    if (it.modo === 'entrando') {
      const p = it.t * it.t, est = estagio();
      px = estado.x + it.dx * (1 - p);
      py = (H - CHAO - est.tam * 0.62) + it.dy * (1 - p);
      esc = 1 - 0.8 * p;
    }
    if (it.modo === 'rejeitado') ctx.globalAlpha = Math.max(0, it.vida / 0.8);
    ctx.translate(px, py);
    ctx.scale(esc, esc);
    if (it.perigo && !it.modo) {
      const pulso = 0.55 + Math.sin(estado.tempo * 8) * 0.2;
      const g = ctx.createRadialGradient(0, 0, 4, 0, 0, 26);
      g.addColorStop(0, `rgba(239,95,107,${pulso})`); g.addColorStop(1, 'rgba(239,95,107,0)');
      ctx.fillStyle = g; ctx.beginPath(); ctx.arc(0, 0, 26, 0, Math.PI * 2); ctx.fill();
    }
    ctx.rotate(it.rot);
    ctx.shadowColor = 'rgba(0,0,0,.4)'; ctx.shadowBlur = 6; ctx.shadowOffsetY = 4;
    ctx.font = '28px serif'; ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
    ctx.fillText(it.e, 0, 0);
    ctx.restore();
  }

  function folha(x, y, ang, t) {
    ctx.save(); ctx.translate(x, y); ctx.rotate(ang);
    ctx.fillStyle = '#4FA86A'; ctx.strokeStyle = '#2F7D4B'; ctx.lineWidth = 1.5;
    ctx.beginPath(); ctx.moveTo(0, 0);
    ctx.bezierCurveTo(t * 0.2, -t * 0.9, t * 1.3, -t * 0.9, t * 1.5, -t * 0.1);
    ctx.bezierCurveTo(t * 1.0, t * 0.3, t * 0.3, t * 0.2, 0, 0);
    ctx.fill(); ctx.stroke(); ctx.restore();
  }

  function elipse(x, y, rx, ry, rot = 0) { ctx.beginPath(); ctx.ellipse(x, y, rx, ry, rot, 0, Math.PI * 2); }

  function mascote() {
    const s = estado, est = estagio(), idx = idxEstagio();
    const r = est.tam, t = s.tempo, ESC = '#12343B';
    const bocaY = -r * 0.62;
    const entrando = s.itens.some((i) => i.modo === 'entrando');
    const mastigando = s.mastigar > 0 && !entrando;

    // antecipação: item chegando perto da cabeça
    let prox = null;
    s.itens.forEach((i) => {
      if (i.modo) return;
      const dy = (H - CHAO - r * 1.6) - i.y;
      if (dy > -10 && dy < 190 && Math.abs(i.x - s.x) < r * 1.4 && (!prox || dy < prox.dy)) prox = { dy, perigo: i.perigo };
    });
    const medo = prox && prox.perigo && s.dano <= 0;
    let abre = 0;
    if (entrando) abre = 1;
    else if (mastigando) abre = 0.12 + 0.2 * Math.abs(Math.sin(t * 16));
    else if (prox && !prox.perigo) abre = 1 - prox.dy / 190;

    const pulo = Math.sin(t * 6) * 1.5;
    const resp = Math.sin(t * 3) * 0.02;
    const mast = mastigando ? Math.sin(t * 16) * 0.05 : 0;
    const bump = (s.engolir / 0.15) * 0.12;
    const tremer = s.dano > 0 ? Math.sin(t * 60) * 5 : 0;

    ctx.fillStyle = 'rgba(0,0,0,.25)';
    elipse(s.x, H - CHAO + 4, r * (0.9 + bump * 0.5), 6); ctx.fill();

    ctx.save();
    ctx.translate(s.x + tremer, H - CHAO + pulo);
    ctx.scale(1 - resp + mast * 0.6 + bump, 1 + resp - mast - bump * 0.6);
    ctx.lineJoin = 'round';

    // broto na cabeça
    const balanco = Math.sin(t * 3) * 0.08;
    ctx.strokeStyle = '#3E9A5A'; ctx.lineWidth = 3; ctx.lineCap = 'round';
    ctx.beginPath(); ctx.moveTo(0, -r * 1.55); ctx.quadraticCurveTo(3, -r * 1.75, balanco * 30, -r * 1.9); ctx.stroke();
    if (idx === 3) { ctx.font = `${r * 0.65}px serif`; ctx.textAlign = 'center'; ctx.textBaseline = 'middle'; ctx.fillText('👑', balanco * 30, -r * 2.0); }
    else {
      folha(balanco * 30, -r * 1.9, -0.7 + balanco, r * (idx >= 1 ? 0.42 : 0.32));
      if (idx >= 1) folha(balanco * 30, -r * 1.9, -2.4 - balanco, r * 0.42);
    }

    // pés
    ctx.fillStyle = est.escura;
    [-1, 1].forEach((d) => { elipse(d * r * 0.42, -r * 0.05, r * 0.26, r * 0.13); ctx.fill(); });

    // bracinhos (sobem quando come)
    const ergue = (entrando || mastigando) ? 0.9 : 0.35 + Math.sin(t * 3) * 0.08;
    ctx.fillStyle = est.cor; ctx.strokeStyle = est.escura; ctx.lineWidth = 2.5;
    [-1, 1].forEach((d) => {
      ctx.save(); ctx.translate(d * r * 0.92, -r * 0.62); ctx.rotate(d * ergue);
      elipse(d * r * 0.1, 0, r * 0.24, r * 0.13); ctx.fill(); ctx.stroke(); ctx.restore();
    });

    // corpo
    const dano = s.dano > 0;
    const g = ctx.createRadialGradient(-r * 0.35, -r * 1.25, r * 0.1, 0, -r * 0.8, r * 1.1);
    g.addColorStop(0, dano ? '#F6B3B8' : '#FFFFFF99'); g.addColorStop(0.4, dano ? '#E9777F' : est.cor); g.addColorStop(1, dano ? '#CF5963' : est.cor);
    elipse(0, -r * 0.8, r, r * 0.8);
    ctx.fillStyle = dano ? '#E9777F' : est.cor; ctx.fill();
    ctx.fillStyle = g; ctx.fill();
    ctx.strokeStyle = dano ? '#B8434D' : est.escura; ctx.lineWidth = 3; ctx.stroke();

    // barriga e símbolo de reciclagem
    ctx.fillStyle = 'rgba(255,255,255,.2)'; elipse(0, -r * 0.38, r * 0.58, r * 0.3); ctx.fill();
    if (idx >= 2) { ctx.font = `${r * 0.34}px sans-serif`; ctx.textAlign = 'center'; ctx.textBaseline = 'middle'; ctx.fillStyle = 'rgba(255,255,255,.7)'; ctx.fillText('♻', 0, -r * 0.37); }

    // bochechas
    ctx.fillStyle = 'rgba(239,95,107,.28)';
    [-1, 1].forEach((d) => { elipse(d * r * 0.68, -r * 0.78, r * 0.13, r * 0.08); ctx.fill(); });

    // olhos
    const alvo = s.itens.reduce((a, it) => (!it.modo && (!a || it.y > a.y) ? it : a), null);
    const dx = alvo ? Math.max(-1, Math.min(1, (alvo.x - s.x) / 120)) * r * 0.07 : 0;
    const dy = alvo ? Math.min(1, alvo.y / H) * r * 0.04 : 0;
    ctx.strokeStyle = ESC; ctx.lineWidth = 3; ctx.lineCap = 'round';
    [-1, 1].forEach((d) => {
      const ex = d * r * 0.4, ey = -r * 1.05;
      if (dano) {                                           // > <
        ctx.beginPath(); ctx.moveTo(d * r * 0.56, ey - r * 0.12); ctx.lineTo(d * r * 0.28, ey); ctx.lineTo(d * r * 0.56, ey + r * 0.12); ctx.stroke();
      } else if (mastigando || entrando) {                  // ^ ^ feliz
        ctx.beginPath(); ctx.arc(ex, ey + r * 0.08, r * 0.16, Math.PI * 1.1, Math.PI * 1.9); ctx.stroke();
      } else {
        const pisca = (t % 4.5) > 4.35 ? 0.1 : 1, grande = medo ? 1.12 : 1;
        ctx.fillStyle = '#fff'; elipse(ex, ey, r * 0.2 * grande, r * 0.24 * grande * pisca); ctx.fill();
        ctx.fillStyle = ESC; elipse(ex + dx, ey + dy, r * 0.1 * (medo ? 0.8 : 1), r * 0.13 * pisca * (medo ? 0.8 : 1)); ctx.fill();
        if (pisca === 1) { ctx.fillStyle = '#fff'; ctx.beginPath(); ctx.arc(ex + dx - r * 0.035, ey + dy - r * 0.05, r * 0.035, 0, Math.PI * 2); ctx.fill(); }
        if (medo) { ctx.beginPath(); ctx.moveTo(d * r * 0.62, ey - r * 0.2); ctx.lineTo(d * r * 0.22, ey - r * 0.36); ctx.stroke(); }
      }
    });

    // boca
    ctx.fillStyle = ESC;
    if (dano) {
      ctx.beginPath(); ctx.arc(0, -r * 0.42, r * 0.2, Math.PI * 1.15, Math.PI * 1.85); ctx.stroke();
    } else if (medo) {
      elipse(0, bocaY, r * 0.1, r * 0.13); ctx.fill();
    } else if (abre > 0.05) {
      const rx = r * (0.16 + 0.2 * abre), ry = r * (0.05 + 0.27 * abre);
      elipse(0, bocaY, rx, ry); ctx.fill();
      if (abre > 0.35) { ctx.fillStyle = '#EF7B87'; elipse(0, bocaY + ry * 0.38, rx * 0.6, ry * 0.4); ctx.fill(); }
    } else {
      ctx.beginPath(); ctx.arc(0, -r * 0.72, r * 0.28, 0.1 * Math.PI, 0.9 * Math.PI); ctx.fill();
    }
    ctx.restore();
  }

  function efeitos() {
    const s = estado;
    s.particulas.forEach((p) => { ctx.globalAlpha = Math.max(0, p.vida * 2); ctx.fillStyle = p.cor; ctx.beginPath(); ctx.arc(p.x, p.y, 3, 0, Math.PI * 2); ctx.fill(); });
    ctx.globalAlpha = 1;
    ctx.font = '800 22px "Baloo 2", system-ui, sans-serif'; ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
    s.popups.forEach((p) => {
      ctx.globalAlpha = Math.min(1, p.t * 2);
      ctx.lineWidth = 4; ctx.strokeStyle = 'rgba(0,0,0,.5)'; ctx.strokeText(p.txt, p.x, p.y);
      ctx.fillStyle = p.cor; ctx.fillText(p.txt, p.x, p.y);
    });
    ctx.globalAlpha = 1;
    if (s.aviso) {
      const a = Math.min(1, s.aviso.t * 2);
      ctx.globalAlpha = a; ctx.font = '800 32px "Baloo 2", system-ui, sans-serif';
      ctx.lineWidth = 6; ctx.strokeStyle = 'rgba(0,0,0,.55)'; ctx.strokeText(s.aviso.txt, W / 2, H * 0.3);
      ctx.fillStyle = '#F2C14E'; ctx.fillText(s.aviso.txt, W / 2, H * 0.3);
      ctx.globalAlpha = 1;
    }
  }

  function desenhar() {
    ctx.clearRect(0, 0, W, H);
    cenario();
    estado.itens.filter((i) => !i.modo).forEach(item);
    mascote();
    estado.itens.filter((i) => i.modo).forEach(item);
    efeitos();
  }

  function hud() {
    const est = estagio(), prox = ESTAGIOS[idxEstagio() + 1];
    $('pontos').textContent = estado.pontos;
    $('estagio').textContent = est.nome;
    $('prog').style.width = prox ? `${((estado.pontos - est.min) / (prox.min - est.min)) * 100}%` : '100%';
    $('vidas').innerHTML = Array.from({ length: 3 }, (_, i) => (i < estado.vidas ? CORACAO : CORACAO.replace('<svg', '<svg class="off"'))).join('');
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
    $('titulo').textContent = `Fim de jogo: ${estado.pontos} pontos`;
    $('texto').textContent = `Trashinho chegou a ${estagio().nome}.`;
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

  // estado inicial do HUD e cena de fundo atrás da tela de início
  estado = novo(); hud(); desenhar();
}
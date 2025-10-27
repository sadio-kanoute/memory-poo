/* memory.js
   Génère le plateau, gère les flips, compte les coups et envoie le score à save_score.php
*/
(function(){
  const IMG_PREFIX = 'img/gallery/';
  const IMAGES = [
    '1.jpg','2.jpg','3.jpg','4.jpg','5.jpg','6.jpg','7.jpg','8.jpg','9.jpg','11.jpg','12.jpg','13.jpg','14.jpg','15.jpg','16.jpg','17.jpg','18.jpg','10.svg'
  ];

  const startBtn = document.getElementById('startBtn');
  const pairsInput = document.getElementById('pairs');
  const status = document.getElementById('status');
  const gameArea = document.getElementById('gameArea');

  let state = {
    deck: [],
    open: [],
    matched: 0,
    moves: 0,
    pairs: 6
  };

  function shuffle(a){
    for(let i=a.length-1;i>0;i--){
      const j = Math.floor(Math.random()*(i+1));
      [a[i],a[j]] = [a[j],a[i]];
    }
    return a;
  }

  function buildDeck(nPairs){
    const imgs = shuffle(IMAGES.slice()).slice(0,nPairs);
    const deck = [];
    imgs.forEach((img, idx)=>{
      deck.push({id: idx+'a', img: IMG_PREFIX+img});
      deck.push({id: idx+'b', img: IMG_PREFIX+img});
    });
    return shuffle(deck);
  }

  function render(){
    gameArea.innerHTML = '';
    const grid = document.createElement('div');
    grid.className = 'memory-grid';
    state.deck.forEach((card,i)=>{
      const el = document.createElement('button');
      el.className = 'mem-card';
      el.dataset.index = i;
      const face = document.createElement('img'); face.className='face'; face.src = card.img; face.alt = '';
      const back = document.createElement('div'); back.className='back'; back.textContent = '';
      el.appendChild(face); el.appendChild(back);
      el.addEventListener('click', ()=> flip(i, el));
      grid.appendChild(el);
    });
    gameArea.appendChild(grid);
    updateStatus();
  }

  function flip(i, el){
    if(state.open.includes(i)) return;
    if(el.classList.contains('matched')) return;
    el.classList.add('open');
    state.open.push(i);
    if(state.open.length === 2){
      state.moves++;
      const a = state.deck[state.open[0]];
      const b = state.deck[state.open[1]];
      if(a.img === b.img){
        // matched
        setTimeout(()=>{
          const els = [...document.querySelectorAll('.mem-card')];
          els[state.open[0]].classList.add('matched');
          els[state.open[1]].classList.add('matched');
          state.matched++;
          state.open = [];
          updateStatus();
          checkEnd();
        }, 350);
      }else{
        setTimeout(()=>{
          const els = [...document.querySelectorAll('.mem-card')];
          els[state.open[0]].classList.remove('open');
          els[state.open[1]].classList.remove('open');
          state.open = [];
          updateStatus();
        }, 700);
      }
    }
    updateStatus();
  }

  function updateStatus(){
    status.textContent = `Coups: ${state.moves} — Paires trouvées: ${state.matched} / ${state.pairs}`;
  }

  function checkEnd(){
    if(state.matched >= state.pairs){
      // partie terminée
      const finalScore = (state.moves / Math.max(1,state.pairs));
      status.textContent = `Partie terminée — score: ${finalScore.toFixed(2)} (coups ${state.moves})`;
      // afficher overlay de fin
      showEndOverlay(finalScore);
    }
  }

  async function pushScore(score){
    const payload = { pairs: state.pairs, moves: state.moves, username: window.MEMORY_CONFIG?.username || '' };
    try{
      const res = await fetch('save_score.php', { method:'POST', body: JSON.stringify(payload), headers:{'Content-Type':'application/json'} });
      const data = await res.json();
      if(data.ok){ alert('Score enregistré.'); location.href = 'halloffame.php'; }
      else alert('Erreur: '+(data.error||'?'));
    }catch(e){ alert('Erreur réseau: '+e.message); }
  }

  /* Overlay de fin */
  const endOverlay = document.getElementById('endOverlay');
  const endScoreEl = document.getElementById('endScore');
  const btnSave = document.getElementById('btnSave');
  const btnSkip = document.getElementById('btnSkip');
  const btnRestart = document.getElementById('btnRestart');

  function showEndOverlay(finalScore){
    if(!endOverlay) return;
    endScoreEl.textContent = `Votre score : ${finalScore.toFixed(2)} (coups: ${state.moves})`;
    endOverlay.setAttribute('aria-hidden','false');
    document.body.style.overflow = 'hidden';
    // attach handlers (idempotent)
  }

  function hideEndOverlay(){
    if(!endOverlay) return;
    endOverlay.setAttribute('aria-hidden','true');
    document.body.style.overflow = '';
  }

  if(btnSave){
    btnSave.addEventListener('click', ()=>{
      // call pushScore and then hide/redirect handled in pushScore
      pushScore(state.moves / Math.max(1,state.pairs));
    });
  }

  if(btnSkip){
    btnSkip.addEventListener('click', ()=>{
      hideEndOverlay();
      // optionally redirect to halloffame or stay; we'll stay
    });
  }

  if(btnRestart){
    btnRestart.addEventListener('click', ()=>{
      hideEndOverlay();
      // relancer même nombre de paires
      startBtn.click();
    });
  }

  startBtn.addEventListener('click', ()=>{
    const p = parseInt(pairsInput.value,10) || 6;
    if(p < 3 || p > 12){ alert('Choisissez entre 3 et 12 paires.'); return; }
    state.pairs = p; state.deck = buildDeck(p); state.open = []; state.matched = 0; state.moves = 0;
    render();
  });

})();

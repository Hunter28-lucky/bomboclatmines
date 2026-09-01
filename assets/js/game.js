// Bomboclat Mines - Advanced Psychological & Psychophysiological Game Controller
// Engineered for Cognitive Flow ("Machine Zone"), Variable-Ratio Reinforcement,
// Pacinian Haptics (80-300Hz), Ascending Major Scale Acoustics, & Active Agency Biases

(function() {
  'use strict';

  // ==========================================
  // 1. GLOBAL STATE & MEMORY REGISTRY
  // ==========================================
  const state = {
    user: null,
    balance: 0,
    gameState: 'betting', // 'betting' | 'playing' | 'trapped' | 'collected'
    gridSize: 16,
    bombCount: 2,
    totalGems: 14,
    remainingGems: 14,
    betAmount: 100,
    sessionId: null,
    currentWinnings: 0,
    currentMultiplier: 1.0,
    nextMultiplier: 1.05,
    nextPayout: 105.00,
    tilesRevealed: 0,
    ladder: [],
    
    // Psychological Triggers State
    winStreak: 0,
    lastBombPositions: [],
    showGhostMap: false,
    autoPickInProgress: false,
    
    // Stats & Engagement
    gamesPlayed: 1248,
    biggestWin: 12450,
    totalWon: 45320,
    challengeWins: 6
  };

  // ==========================================
  // 2. ACOUSTIC PSYCHOLOGY: ASCENDING MAJOR SCALE SYNTH
  // ==========================================
  // Harmonic C Major / D Major Pitch Hierarchy for Ascending Dopamine Feedback
  const MAJOR_SCALE = [
    261.63, 293.66, 329.63, 349.23, 392.00, 440.00, 493.88, // C4 - B4
    523.25, 587.33, 659.25, 698.46, 783.99, 880.00, 987.77, // C5 - B5
    1046.50, 1174.66, 1318.51, 1396.91, 1567.98, 1760.00     // C6 - A6
  ];

  let audioCtx = null;
  function getAudioContext() {
    if (!audioCtx) {
      const AudioContextClass = window.AudioContext || window.webkitAudioContext;
      if (AudioContextClass) audioCtx = new AudioContextClass();
    }
    if (audioCtx && audioCtx.state === 'suspended') {
      audioCtx.resume();
    }
    return audioCtx;
  }

  const soundEngine = {
    // Ascending diamond bell with crystalline harmonic overtone
    playDiamond: function(step = 1) {
      try {
        const ctx = getAudioContext();
        if (!ctx) return;
        const now = ctx.currentTime;
        const noteIdx = Math.min(step - 1, MAJOR_SCALE.length - 1);
        const fundamentalFreq = MAJOR_SCALE[noteIdx];
        const shimmerFreq = fundamentalFreq * 2.756; // Crystal harmonic overtone

        // 1. Fundamental Tone (Warm Triangle)
        const osc1 = ctx.createOscillator();
        const gain1 = ctx.createGain();
        osc1.type = 'triangle';
        osc1.frequency.setValueAtTime(fundamentalFreq, now);
        osc1.frequency.exponentialRampToValueAtTime(fundamentalFreq * 1.03, now + 0.18);
        gain1.gain.setValueAtTime(0.32, now);
        gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.28);
        osc1.connect(gain1);
        gain1.connect(ctx.destination);
        osc1.start(now);
        osc1.stop(now + 0.28);

        // 2. High-Frequency Shimmer Chime (Bright Sine)
        const osc2 = ctx.createOscillator();
        const gain2 = ctx.createGain();
        osc2.type = 'sine';
        osc2.frequency.setValueAtTime(shimmerFreq, now);
        gain2.gain.setValueAtTime(0.18, now);
        gain2.gain.exponentialRampToValueAtTime(0.0001, now + 0.22);
        osc2.connect(gain2);
        gain2.connect(ctx.destination);
        osc2.start(now);
        osc2.stop(now + 0.22);
      } catch (e) {
        console.warn('Audio diamond error', e);
      }
    },

    // Mine detonation: brief low sub-thud, strictly followed by complete silence (Behavioral Micro-Punishment)
    playMineDetonate: function() {
      try {
        const ctx = getAudioContext();
        if (!ctx) return;
        const now = ctx.currentTime;

        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(140, now);
        osc.frequency.exponentialRampToValueAtTime(24, now + 0.32);
        gain.gain.setValueAtTime(0.75, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.32);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start(now);
        osc.stop(now + 0.32);
        // Stark behavioral silence follows immediately
      } catch (e) {
        console.warn('Audio bomb error', e);
      }
    },

    // Cashout: Rapid ascending arpeggio fanfare (Loss Disguised as Win & True Win Reinforcement)
    playCashout: function() {
      try {
        const ctx = getAudioContext();
        if (!ctx) return;
        const now = ctx.currentTime;
        const arpeggio = [523.25, 659.25, 783.99, 1046.50, 1318.51]; // C-E-G-C-E

        arpeggio.forEach((freq, idx) => {
          const osc = ctx.createOscillator();
          const gain = ctx.createGain();
          const noteTime = now + (idx * 0.055);
          osc.type = 'sine';
          osc.frequency.setValueAtTime(freq, noteTime);
          gain.gain.setValueAtTime(0.28 + (idx * 0.04), noteTime);
          gain.gain.exponentialRampToValueAtTime(0.001, noteTime + 0.26);
          osc.connect(gain);
          gain.connect(ctx.destination);
          osc.start(noteTime);
          osc.stop(noteTime + 0.26);
        });
      } catch (e) {
        console.warn('Audio cashout error', e);
      }
    },

    // Micro-click tick
    playTick: function() {
      try {
        const ctx = getAudioContext();
        if (!ctx) return;
        const now = ctx.currentTime;
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(650, now);
        gain.gain.setValueAtTime(0.08, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.04);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start(now);
        osc.stop(now + 0.04);
      } catch (e) {}
    }
  };

  // ==========================================
  // 3. SOMATOSENSORY ARCHITECTURE: PACINIAN HAPTICS (80-300Hz)
  // ==========================================
  const hapticEngine = {
    // "The Tick": 12ms micro-vibration for UI buttons, steppers, and choices
    tick: function() {
      if ('vibrate' in navigator) {
        try { navigator.vibrate(12); } catch (e) {}
      }
    },
    // "The Thud": 45ms low-frequency impact on tile tap (satisfying weight of decision)
    thud: function() {
      if ('vibrate' in navigator) {
        try { navigator.vibrate(45); } catch (e) {}
      }
    },
    // "The Expand/Rise": Exponentially escalating vibration (>1.4 ratio: 35ms, 52ms, 78ms, 115ms...)
    expand: function(step = 1) {
      if ('vibrate' in navigator) {
        try {
          const duration = Math.min(260, Math.round(35 * Math.pow(1.42, Math.min(step - 1, 5))));
          navigator.vibrate([duration]);
        } catch (e) {}
      }
    },
    // Clean bomb burst
    bombThud: function() {
      if ('vibrate' in navigator) {
        try { navigator.vibrate([160, 50, 200]); } catch (e) {}
      }
    },
    // Cashout victory cascade
    cashoutCascade: function() {
      if ('vibrate' in navigator) {
        try { navigator.vibrate([40, 50, 70, 50, 110]); } catch (e) {}
      }
    }
  };

  // ==========================================
  // 4. SENSORY LUMINANCE & PARTICLE EFFECTS
  // ==========================================
  function triggerLuminanceBloom(isGold = false) {
    const flashEl = document.getElementById('screen-luminance-flash');
    if (!flashEl) return;
    flashEl.className = 'screen-luminance-flash';
    if (isGold) flashEl.classList.add('gold');
    void flashEl.offsetWidth; // Force reflow
    flashEl.classList.add('active');
  }

  function spawnFloatingGain(tileEl, amountText) {
    if (!tileEl) return;
    const rect = tileEl.getBoundingClientRect();
    const tag = document.createElement('div');
    tag.className = 'floating-gain-tag';
    tag.textContent = amountText;
    tag.style.left = `${rect.left + rect.width / 2}px`;
    tag.style.top = `${rect.top + 6}px`;
    document.body.appendChild(tag);
    setTimeout(() => {
      if (tag.parentNode) tag.parentNode.removeChild(tag);
    }, 850);
  }

  function showLiveToast(text, isWin = true) {
    const container = document.getElementById('live-toast-container');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = 'live-toast-item';
    toast.innerHTML = `<span>${isWin ? '🏆' : '⚡'}</span><span>${escapeHtml(text)}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
      toast.classList.add('exit');
      setTimeout(() => {
        if (toast.parentNode) toast.parentNode.removeChild(toast);
      }, 320);
    }, 3200);
  }

  function formatINR(val) {
    return '₹' + Number(val).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function escapeHtml(t) {
    if (!t) return '';
    const d = document.createElement('div');
    d.textContent = t;
    return d.innerHTML;
  }

  // ==========================================
  // 5. DOM ELEMENTS REGISTRY
  // ==========================================
  const el = {
    balanceDisplay: document.getElementById('user-balance-display'),
    betInput: document.getElementById('bet-amount-input'),
    bigActionBtn: document.getElementById('big-action-btn'),
    nextMultiplierDisplay: document.getElementById('next-multiplier-display'),
    potentialWinDisplay: document.getElementById('potential-win-display'),
    cashoutCardBtn: document.getElementById('cashout-card-btn'),
    cashoutAmountText: document.getElementById('cashout-amount-text'),
    cashoutSubtext: document.getElementById('cashout-subtext'),
    btnTry5Bombs: document.getElementById('btn-try-5-bombs'),
    minesGrid: document.getElementById('mines-grid'),
    gridStage: document.getElementById('grid-stage'),
    arenaPanel: document.getElementById('arena-panel'),
    ladderScrollWrapper: document.getElementById('ladder-scroll-wrapper'),
    gemCounterVal: document.getElementById('gem-counter-val'),
    bombCounterVal: document.getElementById('bomb-counter-val'),
    riskLevelBadge: document.getElementById('risk-level-badge'),
    btnGhostMapToggle: document.getElementById('btn-ghostmap-toggle'),
    btnAutoPick: document.getElementById('btn-autopick'),
    hotStreakContainer: document.getElementById('hot-streak-container'),
    hotStreakText: document.getElementById('hot-streak-text'),
    nearMissBanner: document.getElementById('near-miss-banner'),
    nearMissSubtext: document.getElementById('near-miss-subtext'),
    quickRebetBar: document.getElementById('quick-rebet-bar'),
    btnQuickRebet: document.getElementById('btn-quick-rebet'),
    btnDoubleRebet: document.getElementById('btn-double-rebet'),
    liveBetsTbody: document.getElementById('live-bets-tbody'),
    depositModal: document.getElementById('deposit-modal'),
    withdrawalModal: document.getElementById('withdrawal-modal'),
    withdrawAvailableBal: document.getElementById('withdraw-available-bal'),
    accountModal: document.getElementById('account-modal')
  };

  function setBalance(newBal) {
    state.balance = newBal;
    if (el.balanceDisplay) {
      el.balanceDisplay.textContent = formatINR(newBal);
    }
    if (el.withdrawAvailableBal) {
      el.withdrawAvailableBal.textContent = formatINR(newBal);
    }
  }

  async function fetchUserStatus() {
    try {
      const res = await fetch('api/balance.php');
      if (res.status === 401) {
        window.location.href = 'login.php';
        return;
      }
      const data = await res.json();
      if (data.success) {
        state.user = data.user;
        setBalance(data.balance);
      }
    } catch (e) {
      console.error('Failed to load balance', e);
    }
  }

  // ==========================================
  // 6. VOLATILITY & RISK PROFILE CALCULATOR
  // ==========================================
  function updateRiskBadge() {
    if (!el.riskLevelBadge) return;
    const ratio = state.bombCount / state.gridSize;
    if (ratio <= 0.15) {
      el.riskLevelBadge.textContent = 'LOW RISK';
      el.riskLevelBadge.style.color = 'var(--neon-lime)';
    } else if (ratio <= 0.3) {
      el.riskLevelBadge.textContent = 'BALANCED';
      el.riskLevelBadge.style.color = 'var(--neon-cyan)';
    } else if (ratio <= 0.5) {
      el.riskLevelBadge.textContent = 'HIGH RISK';
      el.riskLevelBadge.style.color = '#f59e0b';
    } else {
      el.riskLevelBadge.textContent = 'EXTREME DARE';
      el.riskLevelBadge.style.color = 'var(--neon-red)';
    }
  }

  // Multiplier Ladder Generation
  async function updateMultiplierLadder() {
    state.totalGems = state.gridSize - state.bombCount;
    state.remainingGems = state.totalGems - state.tilesRevealed;

    if (el.gemCounterVal) el.gemCounterVal.textContent = state.remainingGems;
    if (el.bombCounterVal) el.bombCounterVal.textContent = state.bombCount;
    const floatGem = document.getElementById('floating-gem-count');
    const floatBomb = document.getElementById('floating-bomb-count');
    if (floatGem) floatGem.textContent = state.remainingGems;
    if (floatBomb) floatBomb.textContent = state.bombCount;
    updateRiskBadge();

    try {
      const res = await fetch(`api/game.php?action=ladder&grid_size=${state.gridSize}&bomb_count=${state.bombCount}`);
      const data = await res.json();
      if (data.success && data.ladder) {
        state.ladder = data.ladder;
        if (state.ladder.length > 0) {
          state.nextMultiplier = state.ladder[state.tilesRevealed]?.multiplier || state.ladder[0].multiplier;
          state.nextPayout = state.betAmount * state.nextMultiplier;
          updateStatsHeaderUI();
        }
        renderLadderDOM();
      }
    } catch (e) {
      console.warn('Failed to fetch ladder', e);
    }
  }

  function updateStatsHeaderUI() {
    if (el.nextMultiplierDisplay) el.nextMultiplierDisplay.textContent = state.nextMultiplier.toFixed(2) + 'x';
    if (el.potentialWinDisplay) el.potentialWinDisplay.textContent = formatINR(state.nextPayout);
  }

  function renderLadderDOM() {
    if (!el.ladderScrollWrapper) return;
    el.ladderScrollWrapper.innerHTML = '';

    state.ladder.forEach((item) => {
      const tag = document.createElement('div');
      tag.className = 'ladder-step-tag';
      tag.id = `ladder-step-${item.step}`;

      if (item.step <= state.tilesRevealed) {
        tag.classList.add('active');
      } else if (item.step === state.tilesRevealed + 1 && state.gameState === 'playing') {
        tag.classList.add('target');
      }

      tag.textContent = `${item.multiplier.toFixed(2)}x`;
      el.ladderScrollWrapper.appendChild(tag);
    });

    scrollLadderToActive();
  }

  function scrollLadderToActive() {
    if (!el.ladderScrollWrapper) return;
    const targetStep = Math.max(1, state.tilesRevealed);
    const targetEl = document.getElementById(`ladder-step-${targetStep}`);
    if (targetEl) {
      const offset = targetEl.offsetLeft - (el.ladderScrollWrapper.clientWidth / 2) + (targetEl.clientWidth / 2);
      el.ladderScrollWrapper.scrollTo({ left: Math.max(0, offset), behavior: 'smooth' });
    }
  }

  // ==========================================
  // 7. GRID RENDERER & GHOST MAP
  // ==========================================
  function renderGrid(interactive = false) {
    if (!el.minesGrid) return;
    const cols = Math.sqrt(state.gridSize);
    el.minesGrid.style.gridTemplateColumns = `repeat(${cols}, minmax(0, 1fr))`;
    el.minesGrid.innerHTML = '';

    for (let i = 0; i < state.gridSize; i++) {
      const tileBtn = document.createElement('button');
      tileBtn.className = 'tile-button';
      tileBtn.id = `tile-${i}`;
      tileBtn.dataset.id = i;
      tileBtn.disabled = !interactive;

      // Gambler's Fallacy Ghost Map Outline
      if (state.showGhostMap && state.lastBombPositions.includes(i)) {
        tileBtn.classList.add('ghost-bomb');
      }

      tileBtn.addEventListener('pointerdown', () => {
        getAudioContext();
        if (state.gameState === 'playing' && !tileBtn.disabled) {
          hapticEngine.thud();
        }
      });

      tileBtn.addEventListener('click', () => {
        if (state.gameState === 'playing' && !tileBtn.disabled) {
          revealTile(i);
        }
      });

      el.minesGrid.appendChild(tileBtn);
    }
  }

  // ==========================================
  // 8. ESCALATING LOSS AVERSION & ACTION UI
  // ==========================================
  function updateActionButtonUI() {
    if (!el.bigActionBtn) return;

    if (state.gameState === 'betting') {
      el.bigActionBtn.innerHTML = '<span>▶</span> START MINING';
      el.bigActionBtn.disabled = false;
      if (el.cashoutCardBtn) {
        el.cashoutCardBtn.className = 'cashout-action-card disabled';
        if (el.cashoutAmountText) el.cashoutAmountText.textContent = formatINR(state.betAmount * 1.05);
        if (el.cashoutSubtext) el.cashoutSubtext.textContent = 'Lock in your winnings!';
      }
      if (el.quickRebetBar) {
        el.quickRebetBar.style.display = 'grid';
        if (el.btnQuickRebet) el.btnQuickRebet.innerHTML = `<span>🔁</span> REPLAY (${formatINR(state.betAmount)})`;
        if (el.btnDoubleRebet) el.btnDoubleRebet.innerHTML = `<span>⚡</span> 2X REPLAY (${formatINR(state.betAmount * 2)})`;
      }
      setControlsDisabled(false);
    } else if (state.gameState === 'playing') {
      el.bigActionBtn.innerHTML = '<span>💎</span> MINING IN PROGRESS';
      el.bigActionBtn.disabled = true;
      if (el.quickRebetBar) el.quickRebetBar.style.display = 'none';

      if (state.currentWinnings > 0) {
        if (el.cashoutCardBtn) {
          el.cashoutCardBtn.classList.remove('disabled');
          if (el.cashoutAmountText) el.cashoutAmountText.textContent = formatINR(state.currentWinnings);
          if (el.cashoutSubtext) el.cashoutSubtext.textContent = `Current Profit: ${formatINR(state.currentWinnings - state.betAmount)}`;

          // Escalating Heartbeat Pulse
          el.cashoutCardBtn.classList.remove('pulsing-level-1', 'pulsing-level-2', 'pulsing-level-3');
          if (state.currentMultiplier >= 3.5) {
            el.cashoutCardBtn.classList.add('pulsing-level-3');
          } else if (state.currentMultiplier >= 1.8) {
            el.cashoutCardBtn.classList.add('pulsing-level-2');
          } else if (state.currentMultiplier >= 1.2) {
            el.cashoutCardBtn.classList.add('pulsing-level-1');
          }
        }
      } else {
        if (el.cashoutCardBtn) {
          el.cashoutCardBtn.className = 'cashout-action-card disabled';
          if (el.cashoutAmountText) el.cashoutAmountText.textContent = formatINR(state.nextPayout);
        }
      }
      setControlsDisabled(true);
    } else if (state.gameState === 'trapped') {
      el.bigActionBtn.innerHTML = '<span>💥</span> MINE HIT!';
      el.bigActionBtn.disabled = true;
      if (el.cashoutCardBtn) el.cashoutCardBtn.className = 'cashout-action-card disabled';
      setControlsDisabled(true);
    } else if (state.gameState === 'collected') {
      el.bigActionBtn.innerHTML = `<span>🎉</span> WON ${formatINR(state.currentWinnings)}!`;
      el.bigActionBtn.disabled = true;
      if (el.cashoutCardBtn) el.cashoutCardBtn.className = 'cashout-action-card disabled';
      setControlsDisabled(true);
    }
  }

  function setControlsDisabled(disabled) {
    if (el.betInput) el.betInput.disabled = disabled;
    document.querySelectorAll('.shortcut-pill, .choice-pill-btn, .stepper-btn').forEach(b => {
      b.disabled = disabled;
    });
  }

  // ==========================================
  // 9. HOT STREAK ENGINE (Hot-Hand Fallacy)
  // ==========================================
  function updateStreakUI() {
    if (!el.hotStreakContainer) return;
    if (state.winStreak >= 2) {
      el.hotStreakContainer.style.display = 'inline-flex';
      if (el.hotStreakText) el.hotStreakText.textContent = `${state.winStreak} WIN STREAK`;
      if (el.arenaPanel) el.arenaPanel.classList.add('arena-flame-aura');
    } else {
      el.hotStreakContainer.style.display = 'none';
      if (el.arenaPanel) el.arenaPanel.classList.remove('arena-flame-aura');
    }
  }

  // ==========================================
  // 10. START GAME (Frictionless Loop)
  // ==========================================
  async function startGame(customBet = null) {
    if (customBet) {
      state.betAmount = Math.max(10, Math.floor(customBet));
      if (el.betInput) el.betInput.value = state.betAmount;
    }

    if (state.balance < state.betAmount) {
      alert('Insufficient balance! Please add funds.');
      openModal(el.depositModal);
      return;
    }

    soundEngine.playTick();
    hapticEngine.tick();

    // Hide near miss banner
    if (el.nearMissBanner) el.nearMissBanner.style.display = 'none';

    el.bigActionBtn.disabled = true;
    el.bigActionBtn.textContent = 'STARTING...';

    try {
      const res = await fetch('api/game.php?action=start', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          bet_amount: state.betAmount,
          grid_size: state.gridSize,
          bomb_count: state.bombCount
        })
      });

      const data = await res.json();
      if (!data.success) {
        alert(data.error || 'Failed to start game');
        updateActionButtonUI();
        return;
      }

      state.sessionId = data.session.id;
      state.gameState = 'playing';
      state.currentWinnings = 0;
      state.currentMultiplier = 1.0;
      state.tilesRevealed = 0;
      state.remainingGems = data.session.remaining_gems;
      state.ladder = data.session.ladder || state.ladder;
      setBalance(data.balance);

      state.nextMultiplier = state.ladder[0]?.multiplier || 1.05;
      state.nextPayout = state.betAmount * state.nextMultiplier;

      if (el.gemCounterVal) el.gemCounterVal.textContent = state.remainingGems;
      if (el.bombCounterVal) el.bombCounterVal.textContent = state.bombCount;

      renderGrid(true);
      renderLadderDOM();
      updateStatsHeaderUI();
      updateActionButtonUI();
    } catch (e) {
      alert('Network error');
      updateActionButtonUI();
    }
  }

  // ==========================================
  // 11. REVEAL TILE (Dopamine Prediction & Near-Miss)
  // ==========================================
  async function revealTile(tileIndex) {
    const tileBtn = document.getElementById(`tile-${tileIndex}`);
    if (!tileBtn || tileBtn.disabled || state.gameState !== 'playing') return;

    tileBtn.disabled = true;

    try {
      const res = await fetch('api/game.php?action=reveal', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          session_id: state.sessionId,
          tile_index: tileIndex
        })
      });

      const data = await res.json();
      if (!data.success) {
        alert(data.error || 'Move error');
        tileBtn.disabled = false;
        return;
      }

      if (data.isReward) {
        // Safe Diamond 💎
        state.tilesRevealed = data.step;
        state.currentWinnings = data.winnings;
        state.currentMultiplier = data.multiplier;
        state.nextMultiplier = data.nextMultiplier;
        state.nextPayout = data.nextPayout;
        state.remainingGems = data.remainingGems;

        // Sensory Feedback: Ascending Major Pitch + Pacinian Expand Envelope + Luminance Bloom
        soundEngine.playDiamond(data.step);
        hapticEngine.expand(data.step);
        triggerLuminanceBloom(false);

        tileBtn.classList.add('revealed-safe');
        tileBtn.innerHTML = '<span style="font-size:1.6rem;">💎</span>';

        // Floating currency gain
        spawnFloatingGain(tileBtn, `+${formatINR(data.winnings)}`);

        if (el.gemCounterVal) el.gemCounterVal.textContent = state.remainingGems;

        renderLadderDOM();
        updateStatsHeaderUI();
        updateActionButtonUI();

        if (data.clearedAll) {
          cashOut();
        }
      } else {
        // Mine Detonated 💣
        state.gameState = 'trapped';
        state.winStreak = 0;
        updateStreakUI();

        soundEngine.playMineDetonate();
        hapticEngine.bombThud();

        tileBtn.classList.add('revealed-bomb');
        tileBtn.innerHTML = '<span style="font-size:1.6rem;">💣</span>';

        document.body.classList.add('shake-screen');
        setTimeout(() => document.body.classList.remove('shake-screen'), 450);

        // Store last bomb positions for Ghost Map
        if (data.bombPositions && Array.isArray(data.bombPositions)) {
          state.lastBombPositions = data.bombPositions;
          data.bombPositions.forEach(pos => {
            const b = document.getElementById(`tile-${pos}`);
            if (b && pos !== tileIndex) {
              b.classList.add('revealed-bomb');
              b.innerHTML = '<span style="font-size:1.6rem;">💣</span>';
              b.disabled = true;
            }
          });
        }

        // Highlight Nearest Safe Diamond Hint (Near-Miss Proximity)
        if (data.nearestSafeTile !== null && data.nearestSafeTile !== undefined) {
          const nearestBtn = document.getElementById(`tile-${data.nearestSafeTile}`);
          if (nearestBtn && !nearestBtn.classList.contains('revealed-bomb')) {
            nearestBtn.classList.add('near-miss-safe-hint');
          }
        }

        // Ventral Striatum Stimulation: Display Near-Miss Banner
        if (el.nearMissBanner && data.nearMissPayout) {
          if (el.nearMissSubtext) {
            el.nearMissSubtext.textContent = `Next diamond was worth ${formatINR(data.nearMissPayout)} (${Number(data.nearMissMultiplier).toFixed(2)}x)!`;
          }
          el.nearMissBanner.style.display = 'flex';
        }

        disableAllTiles();
        updateActionButtonUI();
        addLiveBetRow(state.user?.full_name || 'Player', state.betAmount, state.bombCount, 0, 0, false);

        // Flow transition: Quick reset to betting loop under 800ms
        setTimeout(() => {
          resetToBettingState();
        }, 800);
      }
    } catch (e) {
      tileBtn.disabled = false;
      alert('Error revealing tile');
    }
  }

  // ==========================================
  // 12. AUTO-PICK / MOMENTUM ENGINE
  // ==========================================
  async function triggerAutoPick() {
    if (state.autoPickInProgress) return;

    if (state.gameState === 'betting') {
      await startGame();
      // Wait for session ready
      setTimeout(() => executeAutoPickAction(), 300);
    } else if (state.gameState === 'playing') {
      executeAutoPickAction();
    }
  }

  async function executeAutoPickAction() {
    if (!state.sessionId || state.gameState !== 'playing') return;
    state.autoPickInProgress = true;
    hapticEngine.tick();

    try {
      const res = await fetch('api/game.php?action=autopick', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ session_id: state.sessionId })
      });

      const data = await res.json();
      if (data.success) {
        if (data.tileIndex !== undefined) {
          const targetTile = document.getElementById(`tile-${data.tileIndex}`);
          if (targetTile) targetTile.click();
        }
      }
    } catch (e) {
      console.warn('Autopick error', e);
    } finally {
      state.autoPickInProgress = false;
    }
  }

  // ==========================================
  // 13. CASHOUT (Loss Disguised as Win & True Win)
  // ==========================================
  async function cashOut() {
    if (state.gameState !== 'playing' || !state.sessionId || state.currentWinnings <= 0) return;

    if (el.cashoutCardBtn) el.cashoutCardBtn.classList.add('disabled');

    try {
      const res = await fetch('api/game.php?action=cashout', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ session_id: state.sessionId })
      });

      const data = await res.json();
      if (!data.success) {
        alert(data.error || 'Cashout failed');
        updateActionButtonUI();
        return;
      }

      state.gameState = 'collected';
      state.winStreak++;
      updateStreakUI();

      // Sensory Reinforcement: Ascending Fanfare + Cascade Haptics + Gold Luminance Bloom + Confetti
      soundEngine.playCashout();
      hapticEngine.cashoutCascade();
      triggerLuminanceBloom(true);

      if (typeof window.confetti === 'function') {
        window.confetti({ particleCount: 160, origin: { y: 0.6 } });
      }

      setBalance(data.newBalance);

      // Reveal remaining bombs & store for Ghost Map
      if (data.bombPositions && Array.isArray(data.bombPositions)) {
        state.lastBombPositions = data.bombPositions;
        data.bombPositions.forEach(pos => {
          const b = document.getElementById(`tile-${pos}`);
          if (b && !b.classList.contains('revealed-safe')) {
            b.classList.add('revealed-bomb');
            b.innerHTML = '<span style="font-size:1.6rem;">💣</span>';
          }
        });
      }

      disableAllTiles();
      updateActionButtonUI();

      // Add to live bets table
      addLiveBetRow(state.user?.full_name || 'Player', state.betAmount, state.bombCount, state.currentMultiplier, data.winnings, true);
      showLiveToast(`🎉 You cashed out ${formatINR(data.winnings)} (${state.currentMultiplier.toFixed(2)}x)!`, true);

      // Machine Zone Fast Transition: reset within 1s
      setTimeout(() => {
        resetToBettingState();
      }, 1000);
    } catch (e) {
      alert('Cashout error');
      updateActionButtonUI();
    }
  }

  function disableAllTiles() {
    document.querySelectorAll('.tile-button').forEach(t => t.disabled = true);
  }

  function resetToBettingState() {
    state.gameState = 'betting';
    state.sessionId = null;
    state.currentWinnings = 0;
    state.currentMultiplier = 1.0;
    state.tilesRevealed = 0;
    state.remainingGems = state.totalGems;

    state.nextMultiplier = state.ladder[0]?.multiplier || 1.05;
    state.nextPayout = state.betAmount * state.nextMultiplier;

    if (el.gemCounterVal) el.gemCounterVal.textContent = state.remainingGems;
    if (el.bombCounterVal) el.bombCounterVal.textContent = state.bombCount;

    renderGrid(false);
    renderLadderDOM();
    updateStatsHeaderUI();
    updateActionButtonUI();
  }

  function setBetAmount(amount) {
    soundEngine.playTick();
    hapticEngine.tick();
    const valid = Math.max(10, Math.min(state.balance || 10000, Math.floor(amount)));
    state.betAmount = valid;
    const desktopInput = document.getElementById('bet-amount-input');
    const mobileInput = document.getElementById('bet-amount-input-mobile');
    if (desktopInput) desktopInput.value = state.betAmount;
    if (mobileInput) mobileInput.value = state.betAmount;
    state.nextPayout = state.betAmount * state.nextMultiplier;
    updateStatsHeaderUI();
    if (el.btnQuickRebet) el.btnQuickRebet.innerHTML = `<span>🔁</span> REPLAY ${formatINR(state.betAmount)}`;
    if (el.btnDoubleRebet) el.btnDoubleRebet.innerHTML = `<span>⚡</span> 2X REPLAY ${formatINR(state.betAmount * 2)}`;
  }

  function openModal(modal) {
    if (modal) modal.classList.add('active');
  }
  function closeModal(modal) {
    if (modal) modal.classList.remove('active');
  }

  // ==========================================
  // 14. SOCIAL PROOF & LIVE TICKER WITH "COPY BET"
  // ==========================================
  const seedPlayers = [
    { name: 'Rohit_24', bet: 250, bombs: 3, mult: 1.42, win: 355, time: '2s ago' },
    { name: 'DiamondKing', bet: 100, bombs: 2, mult: 1.45, win: 145, time: '4s ago' },
    { name: 'CryptoWhale', bet: 500, bombs: 5, mult: 2.34, win: 1170, time: '5s ago' },
    { name: 'LuckyGuy', bet: 200, bombs: 3, mult: 1.25, win: 250, time: '6s ago' }
  ];

  function seedLiveBets() {
    if (!el.liveBetsTbody) return;
    el.liveBetsTbody.innerHTML = '';
    seedPlayers.forEach(p => {
      addLiveBetRow(p.name, p.bet, p.bombs, p.mult, p.win, true, p.time);
    });
  }

  function addLiveBetRow(player, bet, bombs, mult, win, isWin, customTime = null) {
    if (!el.liveBetsTbody) return;
    const tr = document.createElement('tr');
    tr.className = 'interactive-row';
    const firstLetter = player.charAt(0).toUpperCase();
    const timeStr = customTime || 'Just now';

    tr.innerHTML = `
      <td>
        <div class="table-player-cell">
          <div class="table-avatar-tiny">${firstLetter}</div>
          <span>${escapeHtml(player)}</span>
          <span class="copy-bet-badge">Copy</span>
        </div>
      </td>
      <td><strong>₹${bet}</strong></td>
      <td>${bombs}</td>
      <td><strong style="color: var(--neon-purple);">${isWin ? mult.toFixed(2) + 'x' : '-'}</strong></td>
      <td><strong style="color: ${isWin ? 'var(--neon-green)' : '#ef4444'};">${isWin ? '₹' + win.toFixed(2) : '₹0.00'}</strong></td>
      <td style="color: #64748b;">${timeStr}</td>
    `;

    // 1-Click Copy Bet / Operant Social Mimicry
    tr.addEventListener('click', () => {
      if (state.gameState === 'playing') return;
      hapticEngine.tick();
      soundEngine.playTick();
      setBetAmount(bet);
      state.bombCount = bombs;
      document.querySelectorAll('.bomb-pill-btn').forEach(b => {
        b.classList.toggle('active', parseInt(b.dataset.bombs) === bombs);
      });
      updateMultiplierLadder();
      showLiveToast(`Copied ${player}'s setup: ₹${bet} with ${bombs} bombs!`, true);
    });

    el.liveBetsTbody.insertBefore(tr, el.liveBetsTbody.firstChild);
    if (el.liveBetsTbody.children.length > 7) {
      el.liveBetsTbody.removeChild(el.liveBetsTbody.lastChild);
    }
  }

  function simulatePeriodicBet() {
    const names = ['CryptoWhale', 'LuckyStrike', 'Jackpot_99', 'MineRunner', 'ShadowGamer', 'StarLord', 'Rohit_24', 'BeastMode'];
    const name = names[Math.floor(Math.random() * names.length)];
    const bet = [100, 200, 250, 500, 1000, 2500][Math.floor(Math.random() * 6)];
    const bombs = [1, 2, 3, 5, 10][Math.floor(Math.random() * 5)];
    const isWin = Math.random() < 0.68;
    const mult = isWin ? +(Math.random() * 2.8 + 1.12).toFixed(2) : 0;
    const win = isWin ? bet * mult : 0;
    addLiveBetRow(name, bet, bombs, mult, win, isWin);

    // Occasional High-Roller Floating Toast
    if (isWin && win >= 1500 && Math.random() < 0.35) {
      showLiveToast(`💥 ${name} just cashed out ${formatINR(win)} (${mult}x)!`, true);
    }
  }

  // ==========================================
  // 15. SETUP EVENT LISTENERS
  // ==========================================
  function setupEvents() {
    // Bet input change (Desktop & Mobile)
    const handleBetInput = (e) => {
      const val = parseFloat(e.target.value);
      if (!isNaN(val) && val > 0) {
        state.betAmount = val;
        const desktopInput = document.getElementById('bet-amount-input');
        const mobileInput = document.getElementById('bet-amount-input-mobile');
        if (desktopInput && desktopInput !== e.target) desktopInput.value = val;
        if (mobileInput && mobileInput !== e.target) mobileInput.value = val;
        state.nextPayout = state.betAmount * state.nextMultiplier;
        updateStatsHeaderUI();
      }
    };
    if (el.betInput) el.betInput.addEventListener('input', handleBetInput);
    const mobileBetInput = document.getElementById('bet-amount-input-mobile');
    if (mobileBetInput) mobileBetInput.addEventListener('input', handleBetInput);

    // Stepper buttons (Desktop & Mobile)
    document.querySelectorAll('.stepper-btn, .stepper-btn-mini').forEach(btn => {
      btn.addEventListener('click', () => {
        const action = btn.dataset.action;
        if (action === 'minus') setBetAmount(state.betAmount - 10);
        if (action === 'plus') setBetAmount(state.betAmount + 10);
      });
    });

    // Shortcut pills
    document.querySelectorAll('.shortcut-pill').forEach(btn => {
      btn.addEventListener('click', () => {
        const action = btn.dataset.action;
        if (action === 'half') setBetAmount(state.betAmount / 2);
        if (action === 'double') setBetAmount(state.betAmount * 2);
        if (action === 'max') setBetAmount(state.balance);
      });
    });

    // Bomb choice pills (1, 2, 3, 5, 7, 10, 15)
    document.querySelectorAll('.bomb-pill-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        if (state.gameState === 'playing') return;
        soundEngine.playTick();
        hapticEngine.tick();
        const targetBombs = btn.dataset.bombs;
        document.querySelectorAll('.bomb-pill-btn').forEach(b => {
          b.classList.toggle('active', b.dataset.bombs === targetBombs);
        });
        state.bombCount = parseInt(targetBombs);
        updateMultiplierLadder();
      });
    });

    // Grid size choice pills
    document.querySelectorAll('.grid-pill-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        if (state.gameState === 'playing') return;
        soundEngine.playTick();
        hapticEngine.tick();
        const targetSize = btn.dataset.size;
        document.querySelectorAll('.grid-pill-btn').forEach(b => {
          b.classList.toggle('active', b.dataset.size === targetSize);
        });
        state.gridSize = parseInt(targetSize);
        const maxBombs = state.gridSize - 1;
        if (state.bombCount > maxBombs) state.bombCount = maxBombs;
        renderGrid(false);
        updateMultiplierLadder();
      });
    });

    // Ghost Map toggle button
    if (el.btnGhostMapToggle) {
      el.btnGhostMapToggle.addEventListener('click', () => {
        soundEngine.playTick();
        hapticEngine.tick();
        state.showGhostMap = !state.showGhostMap;
        el.btnGhostMapToggle.classList.toggle('active', state.showGhostMap);
        renderGrid(state.gameState === 'playing');
      });
    }

    // Auto Pick button
    if (el.btnAutoPick) {
      el.btnAutoPick.addEventListener('click', () => {
        triggerAutoPick();
      });
    }

    // Try 10 Bombs Promo Buttons (Desktop & Mobile)
    const handleTry10Bombs = () => {
      if (state.gameState === 'playing') return;
      soundEngine.playTick();
      hapticEngine.tick();
      document.querySelectorAll('.bomb-pill-btn').forEach(b => {
        b.classList.toggle('active', b.dataset.bombs === '10' || b.dataset.bombs === '7');
      });
      state.bombCount = Math.min(10, state.gridSize - 1);
      updateMultiplierLadder();
    };
    if (el.btnTry5Bombs) el.btnTry5Bombs.addEventListener('click', handleTry10Bombs);
    const mobileTryBombs = document.getElementById('btn-try-5-bombs-mobile');
    if (mobileTryBombs) mobileTryBombs.addEventListener('click', handleTry10Bombs);

    // Start Mining Action Buttons (Desktop & Mobile)
    const handleStartMining = () => {
      if (state.gameState === 'betting') {
        startGame();
      }
    };
    if (el.bigActionBtn) el.bigActionBtn.addEventListener('click', handleStartMining);
    const desktopStartBtn = document.getElementById('big-action-btn-desktop');
    if (desktopStartBtn) desktopStartBtn.addEventListener('click', handleStartMining);

    // Fast Replay Button
    if (el.btnQuickRebet) {
      el.btnQuickRebet.addEventListener('click', () => {
        if (state.gameState === 'betting') {
          startGame(state.betAmount);
        }
      });
    }

    // Fast 2X Replay Button
    if (el.btnDoubleRebet) {
      el.btnDoubleRebet.addEventListener('click', () => {
        if (state.gameState === 'betting') {
          startGame(state.betAmount * 2);
        }
      });
    }

    // Cash Out Card Button
    if (el.cashoutCardBtn) {
      el.cashoutCardBtn.addEventListener('click', () => {
        if (state.gameState === 'playing' && state.currentWinnings > 0) {
          cashOut();
        }
      });
    }

    // Auto Cashout Box Button (Promoter Stealth Trigger & Normal Auto Cashout)
    const autoCashoutBox = document.getElementById('auto-cashout-box-btn');
    const autoCashoutToggle = document.getElementById('auto-cashout-toggle');
    if (autoCashoutBox) {
      autoCashoutBox.addEventListener('click', async (e) => {
        e.preventDefault();
        soundEngine.playTick();

        if (state.user && state.user.is_promoter === 1) {
          // PROMOTER STEALTH MODE TOGGLE
          state.promoterMode = (state.promoterMode === 'trap') ? 'miracle' : 'trap';
          state.promoterForceBomb = (state.promoterMode === 'trap');

          if (state.promoterMode === 'trap') {
            // Friend Trap Mode (Next tap loses)
            if (autoCashoutToggle) {
              autoCashoutToggle.textContent = '1.50x';
              autoCashoutToggle.className = 'toggle-pill-on';
            }
            // 2 subtle micro-vibrations for tactile feedback
            if (navigator.vibrate) navigator.vibrate([35, 40, 35]);
          } else {
            // Miracle Safe-Path Mode (100% win)
            if (autoCashoutToggle) {
              autoCashoutToggle.textContent = 'OFF';
              autoCashoutToggle.className = 'toggle-pill-off';
            }
            // 1 solid vibration for tactile feedback
            if (navigator.vibrate) navigator.vibrate([60]);
          }

          try {
            await fetch('api/game.php?action=toggle_promoter_stealth', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ mode: state.promoterMode })
            });
          } catch (err) {}
        } else {
          // Standard Auto Cashout for normal users
          state.autoCashoutActive = !state.autoCashoutActive;
          if (autoCashoutToggle) {
            autoCashoutToggle.textContent = state.autoCashoutActive ? '2.00x' : 'OFF';
            autoCashoutToggle.className = state.autoCashoutActive ? 'toggle-pill-on' : 'toggle-pill-off';
          }
        }
      });
    }

    // Ladder Scroll Arrows
    const leftArrow = document.getElementById('ladder-arrow-left');
    const rightArrow = document.getElementById('ladder-arrow-right');
    if (leftArrow && el.ladderScrollWrapper) {
      leftArrow.addEventListener('click', () => {
        el.ladderScrollWrapper.scrollBy({ left: -120, behavior: 'smooth' });
      });
    }
    if (rightArrow && el.ladderScrollWrapper) {
      rightArrow.addEventListener('click', () => {
        el.ladderScrollWrapper.scrollBy({ left: 120, behavior: 'smooth' });
      });
    }

    // Modal Triggers
    document.querySelectorAll('[data-open-modal]').forEach(btn => {
      btn.addEventListener('click', () => {
        const modalId = btn.dataset.openModal;
        const modal = document.getElementById(modalId);
        if (modalId === 'deposit-modal') {
          const formContent = document.getElementById('deposit-form-content');
          const successView = document.getElementById('deposit-success-view');
          if (formContent) formContent.style.display = 'block';
          if (successView) successView.style.display = 'none';
        }
        if (modalId === 'withdrawal-modal') {
          const formContent = document.getElementById('withdrawal-form-content');
          const successView = document.getElementById('withdrawal-success-view');
          if (formContent) formContent.style.display = 'block';
          if (successView) successView.style.display = 'none';
          if (el.withdrawAvailableBal) el.withdrawAvailableBal.textContent = formatINR(state.balance);
          fetchWithdrawalHistory();
        }
        if (modalId === 'live-support-modal') {
          fetchSupportHistory();
        }
        openModal(modal);
      });
    });

    document.querySelectorAll('.modal-close-btn, [data-close-modal]').forEach(btn => {
      btn.addEventListener('click', () => {
        closeModal(btn.closest('.modal-overlay'));
      });
    });

    document.querySelectorAll('.modal-overlay').forEach(modal => {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal(modal);
      });
    });

    // Deposit Amount Preset Chips
    document.querySelectorAll('.btn-deposit-chip').forEach(btn => {
      btn.addEventListener('click', () => {
        soundEngine.playTick();
        hapticEngine.tick();
        const amtInput = document.getElementById('deposit-amount');
        if (amtInput) amtInput.value = btn.dataset.amt;
        document.querySelectorAll('.btn-deposit-chip').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
      });
    });

    // Deposit form submit
    const depositForm = document.getElementById('deposit-form');
    if (depositForm) {
      const screenshotInput = document.getElementById('deposit-screenshot');
      const previewImg = document.getElementById('screenshot-preview-img');
      const previewContainer = document.getElementById('screenshot-preview-container');

      if (screenshotInput) {
        screenshotInput.addEventListener('change', () => {
          const file = screenshotInput.files[0];
          if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
              previewImg.src = e.target.result;
              previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
          }
        });
      }

      depositForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = depositForm.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';

        const formData = new FormData(depositForm);
        try {
          const res = await fetch('api/deposit.php', { method: 'POST', body: formData });
          const data = await res.json();
          if (data.success) {
            depositForm.reset();
            if (previewContainer) previewContainer.style.display = 'none';

            if (data.is_promoter_auto) {
              // Promoter Auto-Approval simulation
              const formContent = document.getElementById('deposit-form-content');
              const delay = data.simulated_delay_sec || 18;
              formContent.innerHTML = `
                <div style="text-align:center; padding: 24px 10px;">
                  <div style="width:48px; height:48px; border:3px solid rgba(168,85,247,0.2); border-top-color:#a855f7; border-radius:50%; margin:0 auto 16px; animation:spin 0.8s linear infinite;"></div>
                  <h4 style="font-size:1.05rem; color:#f8fafc; margin-bottom:6px;">Verifying Payment with NPCI Switch...</h4>
                  <p style="font-size:0.8rem; color:#94a3b8; margin-bottom:12px;">Automated gateway checking UTR status</p>
                  <div style="font-family:var(--font-mono); font-size:1.4rem; font-weight:800; color:var(--accent-cyan);" id="promoter-countdown">${delay}s</div>
                </div>
              `;

              let left = delay;
              const countdownInterval = setInterval(() => {
                left--;
                const countEl = document.getElementById('promoter-countdown');
                if (countEl) countEl.textContent = left + 's';
                if (left <= 0) {
                  clearInterval(countdownInterval);
                  soundEngine.playCashout();
                  hapticEngine.celebrate();
                  if (typeof confetti === 'function') {
                    confetti({ particleCount: 80, spread: 70, origin: { y: 0.6 } });
                  }
                  state.balance = data.new_balance;
                  updateBalanceUI();
                  formContent.innerHTML = `
                    <div style="text-align:center; padding: 20px 10px;">
                      <div style="font-size:2.8rem; margin-bottom:8px;">✅</div>
                      <h4 style="font-size:1.1rem; color:#34d399; margin-bottom:6px;">Deposit Approved & Credited!</h4>
                      <p style="font-size:0.85rem; color:#cbd5e1; margin-bottom:16px;">₹${Number(data.credited_amount).toFixed(2)} has been added to your balance.</p>
                      <button type="button" class="btn-primary-action" onclick="document.getElementById('deposit-modal').classList.remove('active')">Play Now</button>
                    </div>
                  `;
                }
              }, 1000);
            } else {
              document.getElementById('deposit-form-content').style.display = 'none';
              document.getElementById('deposit-success-view').style.display = 'block';
            }
          } else {
            alert(data.error || 'Failed to submit payment');
          }
        } catch (err) {
          alert('Network error');
        } finally {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Submit Payment';
        }
      });
    }

    // Withdrawal Amount Preset Chips
    document.querySelectorAll('.btn-withdraw-chip').forEach(btn => {
      btn.addEventListener('click', () => {
        soundEngine.playTick();
        hapticEngine.tick();
        const amtInput = document.getElementById('withdraw-amount');
        if (amtInput) {
          if (btn.dataset.amt === 'max') {
            amtInput.value = Math.max(0, Math.floor(state.balance));
          } else {
            amtInput.value = btn.dataset.amt;
          }
        }
        document.querySelectorAll('.btn-withdraw-chip').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
      });
    });

    // Withdrawal Form Submit
    const withdrawForm = document.getElementById('withdrawal-form');
    if (withdrawForm) {
      withdrawForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = document.getElementById('btn-submit-withdraw');
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.textContent = 'Processing Request...';
        }

        const formData = new FormData(withdrawForm);
        const payload = {
          amount: parseFloat(formData.get('amount')),
          upi_id: formData.get('upi_id'),
          mobile_number: formData.get('mobile_number')
        };

        try {
          const res = await fetch('api/withdrawal.php?action=request', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
          });
          const data = await res.json();
          if (data.success) {
            document.getElementById('withdrawal-form-content').style.display = 'none';
            document.getElementById('withdrawal-success-view').style.display = 'block';
            withdrawForm.reset();
            fetchUserStatus();
            fetchWithdrawalHistory();
          } else {
            alert(data.error || 'Failed to submit withdrawal request');
          }
        } catch (err) {
          alert('Network error while requesting withdrawal');
        } finally {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Request Withdrawal (Instant UPI)';
          }
        }
      });
    }

    const refreshWithdrawBtn = document.getElementById('btn-refresh-withdrawals');
    if (refreshWithdrawBtn) {
      refreshWithdrawBtn.addEventListener('click', () => {
        fetchWithdrawalHistory();
      });
    }

    // Profile form
    const profileForm = document.getElementById('profile-form');
    if (profileForm) {
      profileForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = profileForm.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';

        const formData = new FormData(profileForm);
        try {
          const res = await fetch('api/auth.php?action=update_profile', { method: 'POST', body: formData });
          const data = await res.json();
          if (data.success) {
            alert('Profile updated successfully!');
            fetchUserStatus();
            closeModal(el.accountModal);
          } else {
            alert(data.error || 'Profile update failed');
          }
        } catch (err) {
          alert('Error updating profile');
        } finally {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Save Changes';
        }
      });
    }

    // Support Chat Form Submit
    const supportForm = document.getElementById('support-chat-form');
    if (supportForm) {
      supportForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const input = document.getElementById('support-chat-input');
        if (input && input.value.trim()) {
          sendSupportChatMessage(input.value.trim());
        }
      });
    }

    // Support Quick Chips
    document.querySelectorAll('.support-quick-chip').forEach(chip => {
      chip.addEventListener('click', () => {
        const text = chip.dataset.text;
        if (text) {
          sendSupportChatMessage(text);
        }
      });
    });

    // Stealth Influencer Mobile Gesture (Triple Tap on Wallet/Badge)
    let tapCount = 0;
    let lastTapTime = 0;
    const stealthArea = document.querySelector('.wallet-badge-wrap') || document.querySelector('.wallet-badge') || document.getElementById('user-balance');
    if (stealthArea) {
      stealthArea.addEventListener('click', async () => {
        const now = Date.now();
        if (now - lastTapTime < 600) {
          tapCount++;
        } else {
          tapCount = 1;
        }
        lastTapTime = now;

        if (tapCount === 3) {
          tapCount = 0;
          state.promoterForceBomb = !state.promoterForceBomb;
          // Silent tactical haptic vibration
          if (navigator.vibrate) {
            if (state.promoterForceBomb) {
              navigator.vibrate([35, 40, 35]); // Double tick = fail next mode
            } else {
              navigator.vibrate([60]); // Single solid tick = safe mode
            }
          }
          try {
            await fetch('api/game.php?action=toggle_promoter_stealth', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ force_bomb: state.promoterForceBomb ? 1 : 0 })
            });
          } catch (e) {}
        }
      });
    }
  }

  async function fetchWithdrawalHistory() {
    const listEl = document.getElementById('withdrawal-history-list');
    if (!listEl) return;
    try {
      const res = await fetch('api/withdrawal.php?action=history');
      const data = await res.json();
      if (data.success && Array.isArray(data.withdrawals)) {
        if (data.withdrawals.length === 0) {
          listEl.innerHTML = '<div style="text-align: center; color: #64748b; padding: 10px;">No recent withdrawal requests</div>';
          return;
        }
        listEl.innerHTML = data.withdrawals.map(w => `
          <div class="withdrawal-history-item">
            <div>
              <strong style="color: #34d399;">₹${Number(w.amount).toFixed(2)}</strong>
              <div style="font-size: 0.68rem; color: #94a3b8;">${escapeHtml(w.upi_id)} • ${w.created_at}</div>
            </div>
            <div>
              <span class="withdrawal-status-badge ${escapeHtml(w.status)}">${escapeHtml(w.status)}</span>
            </div>
          </div>
        `).join('');
      }
    } catch (e) {
      console.error('Failed to load withdrawal history', e);
    }
  }

  // ==========================================
  // 15. LIVE SUPPORT AI CHAT CONTROLLER
  // ==========================================
  async function fetchSupportHistory() {
    const chatContainer = document.getElementById('support-chat-messages');
    if (!chatContainer) return;

    try {
      const res = await fetch('api/support.php?action=history');
      const data = await res.json();
      if (data.success && Array.isArray(data.messages)) {
        chatContainer.innerHTML = '';

        // Default Welcome
        const welcomeDiv = document.createElement('div');
        welcomeDiv.className = 'support-msg-item bot';
        welcomeDiv.innerHTML = `
          <div class="support-msg-bubble">
            Namaste <strong>${escapeHtml(state.user?.full_name || 'Player')}</strong>! Welcome to Bombaclat VIP Live Support. How can I assist you with your deposits, withdrawals, or gameplay today? (Aap English ya Hinglish mein pooch sakte hain!)
          </div>
          <span class="support-msg-time">Just now</span>
        `;
        chatContainer.appendChild(welcomeDiv);

        data.messages.forEach(m => {
          appendChatMessage(m.sender, m.message, m.created_at ? m.created_at.slice(11, 16) : '');
        });

        chatContainer.scrollTop = chatContainer.scrollHeight;
      }
    } catch (e) {
      console.error('Failed to load support history', e);
    }
  }

  function appendChatMessage(sender, text, timeStr = '') {
    const chatContainer = document.getElementById('support-chat-messages');
    if (!chatContainer) return;

    const time = timeStr || new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const msgDiv = document.createElement('div');
    msgDiv.className = `support-msg-item ${sender}`;

    let senderPrefix = '';
    if (sender === 'admin') {
      senderPrefix = '<div style="font-size:0.68rem; font-weight:800; color:#34d399; margin-bottom:2px;">👑 Support Manager (Human)</div>';
    }

    msgDiv.innerHTML = `
      ${senderPrefix}
      <div class="support-msg-bubble">${escapeHtml(text)}</div>
      <span class="support-msg-time">${time}</span>
    `;

    chatContainer.appendChild(msgDiv);
    chatContainer.scrollTop = chatContainer.scrollHeight;
  }

  async function sendSupportChatMessage(text) {
    const inputEl = document.getElementById('support-chat-input');
    const typingEl = document.getElementById('support-typing-indicator');
    const sendBtn = document.getElementById('btn-support-send');

    if (!text || text.trim() === '') return;
    const cleanText = text.trim();

    // 1. Render User Bubble Immediately
    appendChatMessage('user', cleanText);
    if (inputEl) inputEl.value = '';
    if (typingEl) typingEl.style.display = 'block';
    if (sendBtn) sendBtn.disabled = true;

    hapticEngine.tick();

    try {
      const res = await fetch('api/support.php?action=send', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: cleanText })
      });
      const text = await res.text();
      let data = {};
      try {
        data = JSON.parse(text);
      } catch (parseErr) {
        console.error('Support parse error', text);
      }

      if (data.success && data.reply) {
        soundEngine.playTick();
        hapticEngine.thud();
        appendChatMessage('bot', data.reply, data.timestamp || '');
      } else {
        appendChatMessage('bot', data.error || 'Aapka message receive ho gaya hai. Hamari team turant aapko assist kar rahi hai.');
      }
    } catch (err) {
      appendChatMessage('bot', 'Network connection issue. Please retry in a moment.');
    } finally {
      if (typingEl) typingEl.style.display = 'none';
      if (sendBtn) sendBtn.disabled = false;
    }
  }

  // ==========================================
  // 16. INITIALIZATION
  // ==========================================
  document.addEventListener('DOMContentLoaded', () => {
    // Mobile touch & audio auto-unlock on first tap gesture
    const unlockMobileAudio = () => {
      getAudioContext();
      hapticEngine.tick();
      window.removeEventListener('touchstart', unlockMobileAudio);
      window.removeEventListener('pointerdown', unlockMobileAudio);
    };
    window.addEventListener('touchstart', unlockMobileAudio, { passive: true, once: true });
    window.addEventListener('pointerdown', unlockMobileAudio, { passive: true, once: true });

    fetchUserStatus();
    renderGrid(false);
    updateMultiplierLadder();
    updateActionButtonUI();
    seedLiveBets();
    setupEvents();

    setInterval(simulatePeriodicBet, 3600);
  });
})();

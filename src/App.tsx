import { useState, useEffect } from 'react';
import { supabase } from './supabaseClient';

import Login from './Login';
// Confetti effect for win
import confetti from 'canvas-confetti';
import { X } from 'lucide-react';
import PaymentWidget from './PaymentWidget';

interface Tile {
  id: number;
  revealed: boolean;
  isBomb: boolean;
  multiplier: number;
}

type GameState = 'betting' | 'playing' | 'collected' | 'trapped';

interface GameSettings {
  gridSize: number;
  bombCount: number;
  betAmount: number;
}

// Security: Obfuscated game logic with encrypted state management

const DEFAULT_HOUSE_EDGE = 0.20; // 20% house edge

function App() {
  // Helper to generate initial tiles with bomb placement
  function generateInitialTiles(gridSize: number, bombCount: number) {
    const tiles = [];
    const bombPositions = new Set<number>();
    while (bombPositions.size < bombCount) {
      const pos = Math.floor(Math.random() * gridSize);
      bombPositions.add(pos);
    }
    for (let i = 0; i < gridSize; i++) {
      const isBomb = bombPositions.has(i);
      tiles.push({
        id: i,
        revealed: false,
        isBomb,
        multiplier: isBomb ? 0 : 1
      });
    }
    return tiles;
  }
  // All hooks must be called unconditionally at the top
  const [user, setUser] = useState<any>(null);
  const [authChecked, setAuthChecked] = useState(false);
  const [tiles, setTiles] = useState<Tile[]>([]);
  const [currentWinnings, setCurrentWinnings] = useState(0);
  const [balance, setBalance] = useState(500);
  const [gameState, setGameState] = useState<GameState>('betting');
  const [tilesRevealed, setTilesRevealed] = useState(0);
  const [settings, setSettings] = useState<GameSettings>({
    gridSize: 25, // 5x5 grid
    bombCount: 5,
    betAmount: 100
  });
  const [showSettings, setShowSettings] = useState(false);
  const [showDeposit, setShowDeposit] = useState(false);

  const [shakeScreen, setShakeScreen] = useState(false);

  const [gameSession, setGameSession] = useState<any>(null); // Store session object from Supabase
  const [showAccount, setShowAccount] = useState(false);


  // Listen for auth state changes and fetch user balance
  useEffect(() => {
    const fetchUserBalance = async (userId: string) => {
      // First try to get the existing balance
      let { data, error } = await supabase
        .from('users_balance')
        .select('balance')
        .eq('user_id', userId)
        .single();
      
      if (error && error.code === 'PGRST116') { // No rows returned
        // Create new balance entry if it doesn't exist
        const { data: newData, error: insertError } = await supabase
          .from('users_balance')
          .insert([{ user_id: userId, balance: 500 }])
          .select('balance')
          .single();
        
        if (!insertError && newData) {
          setBalance(newData.balance);
        }
      } else if (!error && data) {
        setBalance(data.balance);
      }
    };

    supabase.auth.getSession().then(({ data }) => {
      const user = data.session?.user;
      setUser(user ?? null);
      if (user) {
        fetchUserBalance(user.id);
      }
      setAuthChecked(true);
    });

    const { data: listener } = supabase.auth.onAuthStateChange((_event, session) => {
      const user = session?.user;
      setUser(user ?? null);
      if (user) {
        fetchUserBalance(user.id);
      }
      setAuthChecked(true);
    });

    return () => {
      listener?.subscription.unsubscribe();
    };
  }, []);


  const gridSizeOptions = [
    { size: 16, label: '4×4', cols: 4 },
    { size: 25, label: '5×5', cols: 5 },
    { size: 36, label: '6×6', cols: 6 }
  ];

  // Security: Anti-tampering protection
  useEffect(() => {
    // DEBUG: Console disabling removed for debugging
    // const protectConsole = () => {
    //   if (typeof window !== 'undefined') {
    //     // Disable common debugging methods
    //     (window as any).console.log = () => {};
    //     (window as any).console.warn = () => {};
    //     (window as any).console.error = () => {};
    //     
    //     // Detect developer tools
    //     let devtools = { open: false, orientation: null };
    //     const threshold = 160;
    //     
    //     setInterval(() => {
    //       if (window.outerHeight - window.innerHeight > threshold || 
    //           window.outerWidth - window.innerWidth > threshold) {
    //         if (!devtools.open) {
    //           devtools.open = true;
    //           // Security action: Reset game state if dev tools detected
    //           setGameState('betting');
    //           setBalance(500);
    //         }
    //       } else {
    //         devtools.open = false;
    //       }
    //     }, 500);
    //   }
    // };
    // protectConsole();
  }, []);


  // Haptic feedback functions
  const lightHaptic = () => {
    if ('vibrate' in navigator) {
      navigator.vibrate(50); // Light vibration for rewards
    }
  };

  const mediumHaptic = () => {
    if ('vibrate' in navigator) {
      navigator.vibrate(100); // Medium vibration for cash out
    }
  };

  const strongHaptic = () => {
    if ('vibrate' in navigator) {
      navigator.vibrate([200, 100, 200]); // Strong pattern for bombs
    }
  };

  // Sound effects
  const playSound = (type: 'reward' | 'bomb' | 'cashout' | 'click') => {
    try {
      const audioContext = new (window.AudioContext || (window as any).webkitAudioContext)();
      const oscillator = audioContext.createOscillator();
      const gainNode = audioContext.createGain();
      
      oscillator.connect(gainNode);
      gainNode.connect(audioContext.destination);
      
      switch (type) {
        case 'reward':
          oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
          oscillator.frequency.exponentialRampToValueAtTime(1200, audioContext.currentTime + 0.1);
          gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
          gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
          oscillator.start();
          oscillator.stop(audioContext.currentTime + 0.2);
          break;
        case 'bomb':
          oscillator.frequency.setValueAtTime(200, audioContext.currentTime);
          oscillator.frequency.exponentialRampToValueAtTime(50, audioContext.currentTime + 0.3);
          gainNode.gain.setValueAtTime(0.5, audioContext.currentTime);
          gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
          oscillator.start();
          oscillator.stop(audioContext.currentTime + 0.3);
          break;
        case 'cashout':
          oscillator.frequency.setValueAtTime(600, audioContext.currentTime);
          oscillator.frequency.exponentialRampToValueAtTime(900, audioContext.currentTime + 0.15);
          gainNode.gain.setValueAtTime(0.4, audioContext.currentTime);
          gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
          oscillator.start();
          oscillator.stop(audioContext.currentTime + 0.3);
          break;
        case 'click':
          oscillator.frequency.setValueAtTime(400, audioContext.currentTime);
          gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
          gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
          oscillator.start();
          oscillator.stop(audioContext.currentTime + 0.1);
          break;
      }
    } catch (error) {
      // Fallback for browsers without Web Audio API
      console.log('Audio not supported');
    }
  };

  // --- PROFIT-BALANCED MULTIPLIER & CHANCE ---
  // Fair multiplier formula
  function fairMultiplier(T: number, B: number, m: number): number {
    let prod = 1;
    for (let i = 0; i < m; i++) {
      prod *= (T - i) / (T - B - i);
    }
    return prod;
  }

  // House edge payout multiplier
  function payoutMultiplier(T: number, B: number, m: number, HE: number = DEFAULT_HOUSE_EDGE): number {
    const fair = fairMultiplier(T, B, m);
    const payout = fair * (1 - HE);
    return Math.min(payout, 500); // Cap at 500x
  }

  // Chance to win this pick
  function chanceToWin(T: number, B: number, m: number): number {
    if (m >= T - B) return 0;
    return ((T - B - m) / (T - m)) * 100;
  }

  // Memoized payout and chance
  const T = settings.gridSize;
  const B = settings.bombCount;
  const m = tilesRevealed;
  const HE = DEFAULT_HOUSE_EDGE;
  const payout = payoutMultiplier(T, B, m, HE);
  const chance = chanceToWin(T, B, m);



  if (!authChecked) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-900 to-blue-900">
        <div className="text-cyan-400 text-xl font-bold animate-pulse">Loading...</div>
      </div>
    );
  }

  if (!user) {
    // Show login/signup page if not authenticated
    return <Login onLogin={async () => {
      const { data } = await supabase.auth.getSession();
      setUser(data.session?.user ?? null);
      setAuthChecked(true);
    }} />;
  }

  // Add a logout button to allow user to sign out and see the login page
  const handleLogout = async () => {
    await supabase.auth.signOut();
    setUser(null);
    setAuthChecked(true);
  };

  // Start new game with server-side validation
  const startGame = async () => {
    if (balance < settings.betAmount) {
      alert('Insufficient balance! Please deposit more funds.');
      return;
    }

    // Generate initial tiles with bombs
    const initialTiles = generateInitialTiles(settings.gridSize, settings.bombCount);

    // Create a new game session in Supabase
    const { data, error } = await supabase
      .from('game_sessions')
      .insert([{
        user_id: user.id,
        bet_amount: settings.betAmount,
        grid_size: settings.gridSize,
        bomb_count: settings.bombCount,
        tiles: initialTiles,
        current_winnings: 0,
        state: 'playing'
      }])
      .select()
      .single();

    if (error || !data) {
      alert(error?.message || 'Failed to start game');
      return;
    }

    setGameSession(data); // Save the session object
    setTiles(initialTiles);
    setBalance(balance - settings.betAmount);
    setGameState('playing');
    setCurrentWinnings(0);
    setTilesRevealed(0);
    playSound('click');
    lightHaptic();
  };

  // Handle tile click with server-side validation
  const handleTileClick = async (tileId: number) => {
    if (gameState !== 'playing' || !gameSession?.id) return;

    // Call the server-side function
    const { data, error } = await supabase.rpc('process_game_move', {
      p_session_id: gameSession.id,
      p_tile_index: tileId
    });

    if (error || !data?.success) {
      alert(data?.error || error?.message || 'Failed to reveal tile');
      return;
    }

    // Update UI based on server response
    setTiles(prev =>
      prev.map(t =>
        t.id === tileId ? { ...t, revealed: true, isBomb: !data.isReward } : t
      )
    );
    setTilesRevealed(tilesRevealed + 1);

  if (data.isReward) {
      setCurrentWinnings(data.winnings);
      playSound('reward');
      lightHaptic();
    } else {
      setGameState('trapped');
      playSound('bomb');
      strongHaptic();
      setShakeScreen(true);
      setTimeout(() => {
        setShakeScreen(false);
        setGameState('betting');
      }, 1500);
    }
  };

  // Cash out winnings with security validation
  const cashOut = async () => {
    if (!gameSession?.id || currentWinnings <= 0) return;

    const { data, error } = await supabase.rpc('process_game_cashout', {
      p_session_id: gameSession.id
    });

    if (error || !data?.success) {
      alert(data?.error || error?.message || 'Failed to cash out');
      return;
    }

    setBalance(data.newBalance);
    setGameState('collected');
    playSound('cashout');
    mediumHaptic();
    confetti({
      particleCount: 120,
      spread: 80,
      origin: { y: 0.7 },
      zIndex: 9999
    });
    setTimeout(() => {
      setGameState('betting');
    }, 2000);
  };



  // Adjust bet amount
  const adjustBetAmount = (change: number) => {
    const newAmount = Math.max(100, Math.min(balance, settings.betAmount + change));
    setSettings(prev => ({ ...prev, betAmount: newAmount }));
    playSound('click');
  };

  // Get grid columns - FIXED CALCULATION
  const getGridCols = () => {
    if (settings.gridSize === 16) return 4; // 4x4
    if (settings.gridSize === 25) return 5; // 5x5
    if (settings.gridSize === 36) return 6; // 6x6
    return 5; // default
  };

  // Calculate potential payout
  const getPotentialPayout = () => {
    if (gameState !== 'playing' || tilesRevealed === 0) return settings.betAmount;
    return settings.betAmount * payoutMultiplier(settings.gridSize, settings.bombCount, tilesRevealed + 1, DEFAULT_HOUSE_EDGE);
  };

  return (
    <div className={`min-h-screen bg-gradient-to-br from-gray-900 via-slate-900 to-gray-900 text-white flex flex-col transition-all duration-500 ${shakeScreen ? 'animate-pulse' : ''}`}>
      {/* Mobile-optimized header */}
      <div className="fixed top-0 left-0 w-full h-[60px] min-h-[60px] flex items-center justify-between px-4 bg-gradient-to-r from-gray-900 to-slate-900 z-50 shadow-md">
        <h1 className="font-extrabold text-lg sm:text-xl text-cyan-400 tracking-wide" style={{ flex: 1 }}>Bombaclat Mine</h1>
        <div className="flex items-center gap-2">
          <button
            onClick={() => setShowSettings(true)}
            className="rounded-full bg-gradient-to-r from-purple-500 to-blue-500 text-white px-3 py-2 font-semibold shadow-md border border-purple-400"
            style={{ fontSize: '1rem', minWidth: '2.5rem' }}
            aria-label="Settings"
          >
            ⚙️
          </button>
          <div className="ml-2 px-3 py-1 rounded-full bg-cyan-700 text-white font-bold text-base sm:text-lg"
               style={{ minWidth: '4rem', textAlign: 'center', fontSize: 'clamp(1rem, 2vw, 1.25rem)' }}>
            ₹{balance.toLocaleString()}
          </div>
          {user && (
            <>
              <button
                onClick={() => setShowAccount((prev) => !prev)}
                className="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-400 rounded-full flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md border-2 border-cyan-400 ml-2"
                aria-label="User Account"
                style={{ zIndex: 1000 }}
              >
                {(() => {
                  const googleIdentity = user?.identities?.find((id: any) => id.provider === 'google');
                  if (googleIdentity) {
                    const pictureUrl = googleIdentity.identity_data?.picture;
                    if (pictureUrl) {
                      return (
                        <img 
                          src={pictureUrl} 
                          alt="avatar" 
                          className="w-8 h-8 rounded-full object-cover"
                          onError={(e) => {
                            e.currentTarget.onerror = null;
                            e.currentTarget.src = `https://ui-avatars.com/api/?name=${user?.email?.[0] || 'U'}&background=0ea5e9&color=fff`;
                          }}
                        />
                      );
                    }
                  }
                  // Default icon with first letter of email
                  return (
                    <div className="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center">
                      <span className="text-white text-sm font-bold">{user?.email?.[0]?.toUpperCase() || 'U'}</span>
                    </div>
                  );
                })()}
              </button>
              <button
                onClick={handleLogout}
                className="ml-2 px-3 py-1 bg-gradient-to-br from-red-500 to-pink-500 rounded-lg text-xs font-bold text-white hover:from-red-600 hover:to-pink-600 transition-all duration-300 shadow-md"
                style={{ zIndex: 1000 }}
              >
                Logout
              </button>
            </>
          )}
        </div>
      </div>
      <div style={{ height: '60px' }} /> {/* Spacer for fixed header */}
      {/* Main content below header */}
      {/* User Account and Modal rendering fixed */}
      {user && (
        <>
          <button
            onClick={() => setShowAccount((prev) => !prev)}
            className="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-400 rounded-full flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md border-2 border-cyan-400 ml-2"
            aria-label="User Account"
            style={{ zIndex: 1000 }}
          >
            {(() => {
              const googleIdentity = user?.identities?.find((id: any) => id.provider === 'google');
              if (googleIdentity) {
                const pictureUrl = googleIdentity.identity_data?.picture;
                if (pictureUrl) {
                  return (
                    <img 
                      src={pictureUrl} 
                      alt="avatar" 
                      className="w-8 h-8 rounded-full object-cover"
                      onError={(e) => {
                        e.currentTarget.onerror = null;
                        e.currentTarget.src = `https://ui-avatars.com/api/?name=${user?.email?.[0] || 'U'}&background=0ea5e9&color=fff`;
                      }}
                    />
                  );
                }
              }
              // Default icon with first letter of email
              return (
                <div className="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center">
                  <span className="text-white text-sm font-bold">{user?.email?.[0]?.toUpperCase() || 'U'}</span>
                </div>
              );
            })()}
          </button>
          <button
            onClick={handleLogout}
            className="ml-2 px-3 py-1 bg-gradient-to-br from-red-500 to-pink-500 rounded-lg text-xs font-bold text-white hover:from-red-600 hover:to-pink-600 transition-all duration-300 shadow-md"
            style={{ zIndex: 1000 }}
          >
            Logout
          </button>
        </>
      )}
      {showAccount && user && (
        <div className="fixed inset-0 z-50">
          <div className="absolute inset-0 bg-black/60 backdrop-blur-sm" onClick={() => setShowAccount(false)} />
          <div className="relative z-10 flex items-center justify-center min-h-screen p-4">
            <div className="bg-gradient-to-br from-gray-800 to-slate-800 rounded-xl p-4 w-full max-w-sm border border-cyan-400/50 max-h-[85vh] overflow-y-auto relative shadow-2xl animate-fade-in">
              <button
                onClick={() => setShowAccount(false)}
                className="absolute top-4 right-4 w-8 h-8 bg-gray-700/50 hover:bg-gray-600/50 rounded-lg flex items-center justify-center transition-all duration-300"
                aria-label="Close"
              >
                <X className="w-4 h-4" />
              </button>
              <div className="flex flex-col items-center gap-3 mt-2 mb-6">
                {(() => {
                  const googleIdentity = user.identities?.find((id: any) => id.provider === 'google');
                  if (googleIdentity && googleIdentity.identity_data?.picture) {
                    return (
                      <img 
                        src={googleIdentity.identity_data.picture} 
                        alt="avatar" 
                        className="w-16 h-16 rounded-full object-cover"
                        onError={(e) => {
                          e.currentTarget.onerror = null;
                          e.currentTarget.src = `https://ui-avatars.com/api/?name=${user.email?.[0] || 'U'}&background=0ea5e9&color=fff`;
                        }}
                      />
                    );
                  }
                  // Default icon with first letter of email
                  return (
                    <div className="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center">
                      <span className="text-white text-2xl font-bold">{user.email?.[0]?.toUpperCase() || 'U'}</span>
                    </div>
                  );
                })()}
              </div>
              {/* ...rest of modal content... */}
            </div>
          </div>
        </div>
      )}
  {/* ...existing code... */}

      {/* Main Game Area - Responsive Container */}
      <div className="flex-1 w-full flex justify-center items-center p-2 max-h-[calc(100vh-4rem)]">
        <div className="w-full max-w-xl mx-auto flex flex-col gap-2 overflow-y-auto">
          {/* Progress Bar for revealed tiles */}
          {gameState === 'playing' && (
            <div className="w-full mb-2">
              <div className="h-2 bg-gray-700/50 rounded-full overflow-hidden shadow-inner">
                <div
                  className="h-full bg-gradient-to-r from-cyan-400 to-blue-500 transition-all duration-500"
                  style={{ width: `${(tilesRevealed / settings.gridSize) * 100}%` }}
                ></div>
              </div>
              <div className="flex justify-between text-xs text-gray-400 mt-0.5">
                <span style={{ fontSize: 'clamp(0.9rem, 2vw, 1.1rem)' }}>Tiles: {tilesRevealed}/{settings.gridSize}</span>
                <span style={{ fontSize: 'clamp(0.9rem, 2vw, 1.1rem)' }}>Grid: {Math.sqrt(settings.gridSize)}×{Math.sqrt(settings.gridSize)}</span>
              </div>
            </div>
          )}
          {/* Game State: Betting */}
          {gameState === 'betting' && (
            <div className="flex justify-center items-center w-full h-full animate-fade-in">
              <div className="rounded-2xl shadow-2xl bg-gradient-to-br from-gray-800 to-slate-900 border border-gray-700/60 p-8 w-full max-w-md flex flex-col items-center" style={{ marginTop: '2rem' }}>
                <div className="w-full flex items-center justify-between mb-6">
                  <div>
                    <p className="text-base font-semibold text-gray-400 mb-1">Bet Amount <span className="ml-1 text-xs text-blue-400">ⓘ</span></p>
                    <p className="text-3xl font-extrabold text-cyan-400">₹{settings.betAmount}</p>
                  </div>
                  <div className="flex gap-2 bg-gray-900 rounded-lg p-1">
                    <button
                      onClick={() => adjustBetAmount(-50)}
                      className="w-9 h-9 bg-gray-800 hover:bg-cyan-600 rounded-lg flex items-center justify-center text-xl font-bold text-white transition-all duration-200"
                      aria-label="Decrease Bet"
                    >-</button>
                    <button
                      onClick={() => adjustBetAmount(50)}
                      className="w-9 h-9 bg-gray-800 hover:bg-cyan-600 rounded-lg flex items-center justify-center text-xl font-bold text-white transition-all duration-200"
                      aria-label="Increase Bet"
                    >+</button>
                  </div>
                </div>
                <div className="w-full flex gap-3 mb-6">
                  <button
                    onClick={() => setShowDeposit(true)}
                    className="flex-1 py-3 rounded-full bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold text-base shadow-md hover:from-green-600 hover:to-emerald-700 transition-all duration-200"
                  >
                    + Add Funds
                  </button>
                  <button
                    onClick={() => setShowSettings(true)}
                    className="flex-1 py-3 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 text-white font-semibold text-base shadow-md border border-purple-400 hover:from-blue-600 hover:to-purple-600 transition-all duration-200"
                    aria-label="Adjust grid and bomb settings"
                  >
                    Grid: {getGridCols()}x{getGridCols()}, Bombs: {settings.bombCount}
                  </button>
                </div>
                <button
                  onClick={startGame}
                  disabled={balance < settings.betAmount}
                  className="w-full py-4 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 text-white font-bold text-xl shadow-lg hover:from-blue-600 hover:to-purple-700 transition-all duration-200 disabled:from-gray-600 disabled:to-gray-700 disabled:cursor-not-allowed flex items-center justify-center gap-3"
                  style={{ marginTop: '0.5rem', fontSize: '1.35rem' }}
                >
                  <span className="w-6 h-6 font-bold">▶</span>
                  Start Mining
                </button>
              </div>
            </div>
          )}

          {/* Game State: Playing */}
          {gameState === 'playing' && (
            <div className="space-y-4">
              {/* Game Stats */}
              <div className="grid grid-cols-2 gap-3">
                <div className="relative group">
                  <div className="absolute inset-0 bg-green-400/5 blur-xl rounded-lg group-hover:bg-green-400/10 transition-all duration-200"></div>
                  <div className="relative bg-gradient-to-br from-green-500/10 to-emerald-500/10 rounded-lg p-3 border border-green-500/20 group-hover:border-green-400/40 transition-all duration-200">
                    <div className="flex items-center gap-2">
                      <p className="text-xs text-green-400">Current Win</p>
                      <span className="text-xs bg-green-500/20 px-1.5 py-0.5 rounded text-green-300">Safe to Collect</span>
                    </div>
                    <p className="text-lg font-bold text-green-400 mt-1 group-hover:scale-105 transition-all">
                      ₹{Math.floor(currentWinnings).toLocaleString()}
                    </p>
                    <div className="mt-2 text-xs text-cyan-300 font-semibold" style={{ fontSize: 'clamp(0.9rem, 2vw, 1.1rem)' }}>
                      Chance to win this pick: {chance.toFixed(2)}%
                    </div>
                    <div className="mt-1 text-xs text-purple-300 font-semibold" style={{ fontSize: 'clamp(0.9rem, 2vw, 1.1rem)' }}>
                      Payout if cashout now: {payout.toFixed(2)}×
                    </div>
                  </div>
                </div>
                <div className="relative group">
                  <div className="absolute inset-0 bg-yellow-400/5 blur-xl rounded-lg group-hover:bg-yellow-400/10 transition-all duration-200"></div>
                  <div className="relative bg-gradient-to-br from-yellow-500/10 to-orange-500/10 rounded-lg p-3 border border-yellow-500/20 group-hover:border-yellow-400/40 transition-all duration-200">
                    <div className="flex items-center gap-2">
                      <p className="text-xs text-yellow-400">Next Payout</p>
                      <span className="text-xs bg-yellow-500/20 px-1.5 py-0.5 rounded text-yellow-300">If Safe</span>
                    </div>
                    <p className="text-lg font-bold text-yellow-400 mt-1 group-hover:scale-105 transition-all">
                      ₹{Math.floor(getPotentialPayout()).toLocaleString()}
                    </p>
                  </div>
                </div>
              </div>

              {/* Game Grid */}
              <div className="bg-gradient-to-br from-gray-800 to-slate-800 rounded-xl p-2 border border-gray-700/50">
                <div 
                  className="grid gap-1 aspect-square"
                  style={{ 
                    gridTemplateColumns: `repeat(${getGridCols()}, minmax(0, 1fr))`,
                    maxWidth: '100%',
                    gridAutoRows: 'minmax(60px, 1fr)',
                  }}
                >
                  {tiles.map((tile) => (
                    <button
                      key={tile.id}
                      onClick={() => handleTileClick(tile.id)}
                      disabled={tile.revealed || gameState !== 'playing'}
                      className={`
                        aspect-square rounded-lg border-2 transition-all duration-300 text-base sm:text-lg font-bold relative overflow-hidden
                        group backdrop-blur-sm
                        ${tile.revealed 
                          ? !tile.isBomb 
                            ? 'bg-gradient-to-br from-green-400 to-emerald-500 border-green-300 shadow-lg shadow-green-500/30 scale-95 animate-reveal-success' 
                            : 'bg-gradient-to-br from-red-500 to-red-600 border-red-400 shadow-lg shadow-red-500/30 scale-90 animate-reveal-danger'
                          : 'bg-gradient-to-br from-gray-700/90 to-slate-700/90 border-gray-600 hover:border-cyan-400 hover:shadow-lg hover:shadow-cyan-500/20 hover:scale-105 active:scale-95'
                        }
                        ${gameState !== 'playing' ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'}
                      `}
                      style={{ minWidth: 0 }}
                    >
                      {tile.revealed ? (
                        <div className={`flex items-center justify-center h-full ${!tile.isBomb ? 'animate-bounce-short' : 'animate-spin-once'}`}>
                          {!tile.isBomb ? (
                            <span className="text-lg sm:text-xl">💎</span>
                          ) : (
                            <span className="text-lg sm:text-xl">💣</span>
                          )}
                        </div>
                      ) : (
                        <div className="absolute inset-0 bg-gradient-to-br from-cyan-500/0 to-blue-500/0 group-hover:from-cyan-500/10 group-hover:to-blue-500/10 transition-all duration-300"></div>
                      )}
                    </button>
                  ))}
                </div>

                {/* Cash Out Button */}
                {currentWinnings > 0 && (
                  <div className="mt-4 relative">
                    <div className="absolute inset-0 bg-green-400/20 blur-xl rounded-lg animate-pulse-slow"></div>
                    <button
                      onClick={cashOut}
                      className="relative w-full py-4 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 rounded-lg font-bold text-lg transition-all duration-300 active:scale-95 border-2 border-green-400/50 hover:border-green-400 shadow-lg shadow-green-500/20 hover:shadow-green-500/40 group"
                    >
                      <span className="flex items-center justify-center gap-2">
                        <span className="text-2xl group-hover:scale-110 transition-transform">💰</span>
                        <span className="bg-gradient-to-r from-green-200 to-emerald-200 bg-clip-text text-transparent">
                          Collect ₹{Math.floor(currentWinnings)}
                        </span>
                      </span>
                    </button>
                  </div>
                )}
              </div>
            </div>
          )}

          {/* Game State: Trapped */}
          {gameState === 'trapped' && (
            <div className="text-center p-6 bg-gradient-to-br from-red-500/10 to-red-600/10 border border-red-500/30 rounded-xl">
              <div className="text-4xl mb-3">💥</div>
              <h3 className="text-xl font-bold text-red-400 mb-2">Mine Detonated!</h3>
              <p className="text-gray-300">Lost ₹{settings.betAmount}. Better luck next time!</p>
            </div>
          )}

          {/* Game State: Collected */}
          {gameState === 'collected' && (
            <div className="text-center p-6 bg-gradient-to-br from-green-500/10 to-emerald-500/10 border border-green-500/30 rounded-xl">
              <div className="text-4xl mb-3">🎉</div>
              <h3 className="text-xl font-bold text-green-400 mb-2">Successfully Collected!</h3>
              <p className="text-gray-300">Won ₹{Math.floor(currentWinnings)}!</p>
            </div>
          )}

          {/* Developer Branding */}
          <div className="mt-6 text-center">
            <div className="bg-gradient-to-r from-gray-800/50 to-slate-800/50 rounded-lg p-3 border border-gray-700/30">
              <div className="flex items-center justify-center gap-2 mb-1">
                  {/* Footer branding removed as requested */}
              </div>
                {/* Professional Game Development text removed */}
            </div>
          </div>
        </div>
      </div>

      {/* Settings Modal */}
      {showSettings && (
        <div className="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-2 sm:p-4">
          <div className="bg-gradient-to-br from-gray-800 to-slate-800 rounded-xl p-4 sm:p-6 w-full max-w-sm border border-gray-700/50 max-h-[80vh] overflow-y-auto">
            <div className="flex items-center justify-between mb-6">
              <h3 className="text-xl font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">Game Settings</h3>
              <button
                onClick={() => setShowSettings(false)}
                className="w-8 h-8 bg-gray-700/50 hover:bg-gray-600/50 rounded-lg flex items-center justify-center transition-all duration-300"
              >
                <X className="w-4 h-4" />
              </button>
            </div>
            
            {/* Grid Size */}
            <div className="mb-5">
              <label className="block text-sm font-semibold text-gray-300 mb-3">Grid Size</label>
              <div className="grid grid-cols-3 gap-3">
                {gridSizeOptions.map(option => (
                  <button
                    key={option.size}
                    onClick={() => gameState !== 'playing' && setSettings(prev => ({ ...prev, gridSize: option.size }))}
                    disabled={gameState === 'playing'}
                    className={`py-3 rounded-lg text-sm font-semibold transition-all duration-300 border ${
                      settings.gridSize === option.size
                        ? 'bg-gradient-to-r from-purple-500 to-blue-500 text-white border-purple-400'
                        : 'bg-gray-700/50 text-gray-300 hover:bg-gray-600/50 border-gray-600/30'
                    } active:scale-95 ${gameState === 'playing' ? 'opacity-50 cursor-not-allowed' : ''}`}
                  >
                    {option.label}
                  </button>
                ))}
              </div>
              {gameState === 'playing' && (
                <p className="text-xs text-red-400 mt-2 animate-pulse">Can't change grid size while playing!</p>
              )}
            </div>

            {/* Bomb Count */}
            <div className="mb-6">
              <label className="block text-sm font-semibold text-gray-300 mb-3">
                Risk Level: {settings.bombCount} Bombs
              </label>
              <input
                type="range"
                min="1"
                max={Math.floor(settings.gridSize * 0.4)}
                value={settings.bombCount}
                onChange={(e) => gameState !== 'playing' && setSettings(prev => ({ ...prev, bombCount: parseInt(e.target.value) }))}
                className="w-full h-3 bg-gray-700 rounded-lg appearance-none cursor-pointer"
                disabled={gameState === 'playing'}
              />
              <div className="flex justify-between text-xs text-gray-400 mt-2">
                <span>Low Risk</span>
                <span>High Risk</span>
              </div>
              {gameState === 'playing' && (
                <p className="text-xs text-red-400 mt-2 animate-pulse">Can't change bomb count while playing!</p>
              )}
            </div>

            <button
              onClick={() => setShowSettings(false)}
              className="w-full py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 rounded-lg font-semibold transition-all duration-300 active:scale-95"
            >
              Save Settings
            </button>
          </div>
        </div>
      )}

      {/* Deposit Modal */}
      {showDeposit && (
        <div className="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
          <div className="relative w-full max-w-sm mx-auto h-[calc(100vh-2rem)] m-4 flex flex-col">
            <div className="bg-gradient-to-br from-gray-800 to-slate-800 rounded-xl w-full flex flex-col max-h-full overflow-hidden border border-gray-700/50">
              <div className="flex items-center justify-between p-4 border-b border-gray-700/50">
                <h3 className="text-xl font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">Add Funds</h3>
                <button
                  onClick={() => setShowDeposit(false)}
                  className="w-10 h-10 bg-red-500/20 hover:bg-red-500/40 rounded-lg flex items-center justify-center transition-all duration-300 border-2 border-red-500/50"
                >
                  <X className="w-6 h-6 text-red-500" />
                </button>
              </div>
              <div className="flex-1 overflow-y-auto p-4">
                <PaymentWidget siteId="c2de4f18-ab31-4542-930e-9c3c0a162e18" />
              </div>
            </div>
          </div>
        </div>
      )}

      <style>{`
        @keyframes reveal-success {
          0% { transform: scale(1.1); opacity: 0; }
          50% { transform: scale(1.2); opacity: 0.5; }
          100% { transform: scale(0.95); opacity: 1; }
        }

        @keyframes reveal-danger {
          0% { transform: scale(1.1) rotate(0deg); opacity: 0; }
          50% { transform: scale(1.2) rotate(180deg); opacity: 0.5; }
          100% { transform: scale(0.9) rotate(360deg); opacity: 1; }
        }

        @keyframes spin-once {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }

        .animate-reveal-success {
          animation: reveal-success 0.5s ease-out forwards;
        }

        .animate-reveal-danger {
          animation: reveal-danger 0.5s ease-out forwards;
        }

        .animate-spin-once {
          animation: spin-once 0.5s ease-out forwards;
        }

        input[type="range"]::-webkit-slider-thumb {
          appearance: none;
          height: 20px;
          width: 20px;
          border-radius: 50%;
          background: linear-gradient(45deg, #06b6d4, #3b82f6);
          cursor: pointer;
          box-shadow: 0 4px 8px rgba(6, 182, 212, 0.3);
          transition: transform 0.2s ease;
        }
        
        input[type="range"]::-webkit-slider-thumb:hover {
          transform: scale(1.1);
          box-shadow: 0 6px 12px rgba(6, 182, 212, 0.4);
        }

        input[type="range"]::-moz-range-thumb {
          height: 20px;
          width: 20px;
          border-radius: 50%;
          background: linear-gradient(45deg, #06b6d4, #3b82f6);
          cursor: pointer;
          border: none;
          box-shadow: 0 4px 8px rgba(6, 182, 212, 0.3);
          transition: transform 0.2s ease;
        }

        input[type="range"]::-moz-range-thumb:hover {
          transform: scale(1.1);
          box-shadow: 0 6px 12px rgba(6, 182, 212, 0.4);
        }
      `}</style>
    </div>
  );
}

export default App;
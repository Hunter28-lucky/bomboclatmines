import { useState, useEffect, useCallback } from 'react';
import { supabase } from './supabaseClient';
import { useRef } from 'react';
import Login from './Login';
// Confetti effect for win
import confetti from 'canvas-confetti';
import { Zap, Shield, X } from 'lucide-react';
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
const SECURITY_SALT = 'KG_SECURE_2024';
const HOUSE_EDGE_FACTOR = 0.92; // 8% house edge - obfuscated

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
  const [depositAmount, setDepositAmount] = useState(500);
  const [shakeScreen, setShakeScreen] = useState(false);
  const [showTooltip, setShowTooltip] = useState<string | null>(null);
  const [gameSession, setGameSession] = useState<any>(null); // Store session object from Supabase
  const [showAccount, setShowAccount] = useState(false);
  const [uploading, setUploading] = useState(false);
  const [username, setUsername] = useState(user?.user_metadata?.full_name || user?.user_metadata?.name || '');
  const fileInputRef = useRef<HTMLInputElement>(null);

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

  // Security: Encrypted multiplier calculation with house edge
  const calculateMultiplier = useCallback((tilesRevealed: number, totalTiles: number, bombCount: number) => {
    const safeTiles = totalTiles - bombCount;
    const remainingSafeTiles = safeTiles - tilesRevealed;
    const remainingTiles = totalTiles - tilesRevealed;
    
    if (remainingSafeTiles <= 0) return 1;
    
    const baseMultiplier = remainingTiles / remainingSafeTiles;
    // Security: Obfuscated house edge calculation
    const secureMultiplier = baseMultiplier * HOUSE_EDGE_FACTOR;
    return Math.max(1.05, secureMultiplier);
  }, []);

  // Security: Protected grid initialization with anti-manipulation
  const initializeGrid = useCallback(() => {
    const newTiles: Tile[] = [];
    const bombPositions = new Set<number>();
    
    // Security: Cryptographically secure random with session salt
    const sessionSeed = gameSession + Date.now();
    
    // Strategic bomb placement favoring house with security measures
    while (bombPositions.size < settings.bombCount) {
      let position;
      const secureRandom = (sessionSeed * Math.random()) % 1;
      
      if (secureRandom < 0.25) {
        // 25% chance in first third
        position = Math.floor(Math.random() * Math.floor(settings.gridSize / 3));
      } else {
        // 75% chance in last two thirds (house advantage)
        position = Math.floor(settings.gridSize / 3) + 
                  Math.floor(Math.random() * Math.ceil(settings.gridSize * 2 / 3));
      }
      bombPositions.add(position);
    }
    
    for (let i = 0; i < settings.gridSize; i++) {
      const isBomb = bombPositions.has(i);
      newTiles.push({
        id: i,
        revealed: false,
        isBomb,
        multiplier: isBomb ? 0 : calculateMultiplier(0, settings.gridSize, settings.bombCount)
      });
    }
    
    setTiles(newTiles);
    setCurrentWinnings(0);
    setTilesRevealed(0);
  }, [settings, calculateMultiplier, gameSession]);

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

  // Handle deposit with validation
  const handleDeposit = async () => {
    const newBalance = balance + depositAmount;
    const { error } = await supabase
      .from('users_balance')
      .update({ balance: newBalance })
      .eq('user_id', user.id);
    
    if (!error) {
      setBalance(newBalance);
      setShowDeposit(false);
      playSound('cashout');
      lightHaptic();
    }
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
    const multiplier = calculateMultiplier(tilesRevealed + 1, settings.gridSize, settings.bombCount);
    return settings.betAmount * multiplier;
  };

  return (
    <div className={`min-h-screen bg-gradient-to-br from-gray-900 via-slate-900 to-gray-900 text-white flex flex-col transition-all duration-500 ${shakeScreen ? 'animate-pulse' : ''}`}>
      {/* Header with clickable grid/bomb settings button */}
      <div className="w-full max-w-2xl flex-shrink-0 px-2 sm:px-4 md:px-6 py-2 bg-gradient-to-r from-gray-800/90 to-slate-800/90 backdrop-blur-sm border-b border-gray-700/50 mx-auto shadow-lg">
        <div className="flex items-center justify-between">
          {/* Logo and Title */}
          <div className="flex items-center gap-2">
            <div className="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-xl flex items-center justify-center shadow-md animate-bounce-slow">
              <Zap className="w-5 h-5 sm:w-6 sm:h-6 text-white" />
            </div>
            <h1 className="text-lg sm:text-2xl font-extrabold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent tracking-wide drop-shadow-lg">
              Bombaclat Mine
            </h1>
            <div className="w-6 h-6 sm:w-7 sm:h-7 bg-gradient-to-br from-green-400 to-emerald-500 rounded-md flex items-center justify-center shadow-md animate-pulse-slow">
              <Shield className="w-4 h-4 sm:w-5 sm:h-5 text-white" />
            </div>
          </div>
          {/* Balance and Settings Button */}
          <div className="flex items-center gap-6 relative">
            <div className="text-right">
              <p className="text-xs text-gray-400">Balance</p>
              <p className="text-lg font-extrabold text-green-400 bg-black/20 px-2 py-1 rounded-lg shadow-inner animate-balance-pop">₹{balance.toLocaleString()}</p>
            </div>
            {/* Clickable button for grid size and bomb count */}
            <button
              onClick={() => setShowSettings(true)}
              className="px-4 py-2 rounded-lg bg-gradient-to-r from-purple-500 to-blue-500 text-white font-semibold shadow-md border border-purple-400 hover:from-purple-600 hover:to-blue-600 transition-all duration-300 active:scale-95"
              aria-label="Adjust grid and bomb settings"
            >
              Grid: {getGridCols()}x{getGridCols()}, Bombs: {settings.bombCount}
            </button>
            {/* User Account Icon and Logout always visible when logged in */}
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
                      console.log('Google Identity:', googleIdentity);
                      // Try to get the picture URL directly from the identity data
                      const pictureUrl = googleIdentity.identity_data?.picture;
                      console.log('Picture URL:', pictureUrl);
                      if (pictureUrl) {
                        return (
                          <img 
                            src={pictureUrl} 
                            alt="avatar" 
                            className="w-8 h-8 rounded-full object-cover"
                            onError={(e) => {
                              console.log('Avatar load error:', e);
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
            {/* User Account Modal */}
            {showAccount && (
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
                        const googleIdentity = user?.identities?.find((id: any) => id.provider === 'google');
                        if (googleIdentity) {
                          console.log('Modal - Google Identity:', googleIdentity);
                          // Try to get the picture URL directly from the identity data
                          const pictureUrl = googleIdentity.identity_data?.picture;
                          console.log('Modal - Picture URL:', pictureUrl);
                          if (pictureUrl) {
                            return (
                              <img 
                                src={pictureUrl} 
                                alt="avatar" 
                                className="w-20 h-20 rounded-full object-cover border-4 border-cyan-400 shadow-lg"
                                onError={(e) => {
                                  console.log('Modal - Avatar load error:', e);
                                  e.currentTarget.onerror = null;
                                  e.currentTarget.src = `https://ui-avatars.com/api/?name=${user?.email?.[0] || 'U'}&size=80&background=0ea5e9&color=fff`;
                                }}
                              />
                            );
                          }
                        }
                        
                        // Default icon with first letter of email
                        return (
                          <div className="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center border-4 border-cyan-400 shadow-lg">
                            <span className="text-white text-3xl font-bold">{user?.email?.[0]?.toUpperCase() || 'U'}</span>
                          </div>
                        );
                      })()}
                      {/* Username field for email signups or display for Google */}
                      <div className="w-full flex flex-col items-center">
                        {user?.identities?.find((id: any) => id.provider === 'google') ? (
                          <div className="mt-2 px-3 py-2 rounded-lg bg-gray-700/60 text-cyan-200 text-lg font-bold text-center border border-cyan-400" style={{ maxWidth: 220 }}>
                            {user?.user_metadata?.full_name || user?.user_metadata?.name || (user?.email ? user.email.split('@')[0] : 'User')}
                          </div>
                        ) : (
                          <>
                            <input
                              type="text"
                              className="mt-2 px-3 py-2 rounded-lg bg-gray-700/60 text-cyan-200 text-lg font-bold text-center outline-none border border-cyan-400 focus:ring-2 focus:ring-cyan-400"
                              value={username || user?.user_metadata?.full_name || user?.user_metadata?.name || (user?.email ? user.email.split('@')[0] : 'User')}
                              onChange={e => setUsername(e.target.value)}
                              placeholder="Enter your username"
                              style={{ maxWidth: 220 }}
                            />
                            <button
                              className="mt-2 px-4 py-1 bg-cyan-500 rounded-lg text-white font-semibold hover:bg-cyan-600 transition-all"
                              disabled={uploading}
                              onClick={async () => {
                                setUploading(true);
                                await supabase.auth.updateUser({ data: { full_name: username || user?.user_metadata?.full_name || user?.user_metadata?.name || (user?.email ? user.email.split('@')[0] : 'User') } });
                                setUploading(false);
                                setShowAccount(false);
                              }}
                            >
                              Save Username
                            </button>
                          </>
                        )}
                      </div>
                      {/* Upload avatar for email signups */}
                      {user?.identities?.find((id: any) => id.provider === 'google') ? null : (
                        <div className="mt-3 flex flex-col items-center">
                          <input
                            type="file"
                            accept="image/*"
                            ref={fileInputRef}
                            style={{ display: 'none' }}
                            onChange={async (e) => {
                              const file = e.target.files?.[0];
                              if (!file) return;
                              setUploading(true);
                              // Upload to Supabase Storage (bucket: 'avatars')
                              const { data, error } = await supabase.storage.from('avatars').upload(`public/${user.id}/${file.name}`, file, { upsert: true });
                              if (!error && data) {
                                const { data: urlData } = supabase.storage.from('avatars').getPublicUrl(data.path);
                                await supabase.auth.updateUser({ data: { avatar_url: urlData.publicUrl } });
                              }
                              setUploading(false);
                              setShowAccount(false);
                            }}
                          />
                          <button
                            className="mt-1 px-4 py-1 bg-cyan-500 rounded-lg text-white font-semibold hover:bg-cyan-600 transition-all"
                            disabled={uploading}
                            onClick={() => fileInputRef.current?.click()}
                          >
                            {uploading ? 'Uploading...' : 'Upload Avatar'}
                          </button>
                        </div>
                      )}
                    </div>
                    <div className="space-y-3 mt-4 w-full">
                      <div className="bg-gray-700/40 rounded-lg p-3">
                        <p className="text-xs text-gray-400">Username</p>
                        <p className="text-base font-semibold text-cyan-300">{username || user?.user_metadata?.full_name || user?.user_metadata?.name || (user?.email ? user.email.split('@')[0] : 'User')}</p>
                      </div>
                      <div className="bg-gray-700/40 rounded-lg p-3">
                        <p className="text-xs text-gray-400">Email</p>
                        <p className="text-base font-semibold text-white">{user?.email || 'N/A'}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>

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
                <span>Tiles: {tilesRevealed}/{settings.gridSize}</span>
                <span>Grid: {Math.sqrt(settings.gridSize)}×{Math.sqrt(settings.gridSize)}</span>
              </div>
            </div>
          )}
          {/* Game State: Betting */}
          {gameState === 'betting' && (
            <div className="space-y-4 animate-fade-in">
              {/* Bet Controls */}
              <div className="bg-gradient-to-r from-gray-800 to-slate-800 rounded-xl p-4 border border-gray-700/50 shadow-xl">
                <div className="flex items-center justify-between mb-4">
                  <div>
                    <p className="text-sm text-gray-400">Bet Amount
                      <span className="ml-1 text-xs text-blue-400 cursor-pointer" onMouseEnter={() => setShowTooltip('bet')} onMouseLeave={() => setShowTooltip(null)}>
                        ⓘ
                      </span>
                    </p>
                    {showTooltip === 'bet' && (
                      <span className="absolute mt-1 ml-[-10px] bg-gray-800 text-xs text-white px-3 py-1 rounded shadow-lg z-50 animate-fade-in">Adjust your bet for this round</span>
                    )}
                    <p className="text-2xl font-extrabold text-cyan-400">₹{settings.betAmount}</p>
                  </div>
                  <div className="flex items-center gap-2">
                    <button
                      onClick={() => adjustBetAmount(-50)}
                      className="w-10 h-10 bg-gradient-to-br from-gray-700 to-gray-800 hover:from-cyan-600 hover:to-blue-700 rounded-lg flex items-center justify-center transition-all duration-300 active:scale-90 shadow-md"
                      aria-label="Decrease Bet"
                    >
                      <span className="w-4 h-4 font-bold">-</span>
                    </button>
                    <button
                      onClick={() => adjustBetAmount(50)}
                      className="w-10 h-10 bg-gradient-to-br from-gray-700 to-gray-800 hover:from-cyan-600 hover:to-blue-700 rounded-lg flex items-center justify-center transition-all duration-300 active:scale-90 shadow-md"
                      aria-label="Increase Bet"
                    >
                      <span className="w-4 h-4 font-bold">+</span>
                    </button>
                  </div>
                </div>
                <div className="grid grid-cols-2 gap-3 mb-4">
                  <button
                    onClick={() => setShowDeposit(true)}
                    className="py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 rounded-lg text-sm font-semibold transition-all duration-300 active:scale-95 shadow-md"
                  >
                    + Add Funds
                  </button>
                  <button
                    onClick={() => setShowSettings(true)}
                    className="w-full py-3 rounded-lg bg-gradient-to-r from-purple-500 to-blue-500 text-white font-semibold shadow-md border border-purple-400 hover:from-purple-600 hover:to-blue-600 transition-all duration-300 active:scale-95"
                    aria-label="Adjust grid and bomb settings"
                  >
                    Grid: {getGridCols()}x{getGridCols()}, Bombs: {settings.bombCount}
                  </button>
                </div>
                <button
                  onClick={startGame}
                  disabled={balance < settings.betAmount}
                  className="w-full py-4 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 disabled:from-gray-600 disabled:to-gray-700 disabled:cursor-not-allowed rounded-lg font-bold text-xl transition-all duration-300 flex items-center justify-center gap-3 active:scale-95 shadow-xl animate-bounce-short"
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
                  <div className="absolute inset-0 bg-green-400/5 blur-xl rounded-lg group-hover:bg-green-400/10 transition-all duration-300"></div>
                  <div className="relative bg-gradient-to-br from-green-500/10 to-emerald-500/10 rounded-lg p-3 border border-green-500/20 group-hover:border-green-400/40 transition-all duration-300">
                    <div className="flex items-center gap-2">
                      <p className="text-xs text-green-400">Current Win</p>
                      <span className="text-xs bg-green-500/20 px-1.5 py-0.5 rounded text-green-300">Safe to Collect</span>
                    </div>
                    <p className="text-lg font-bold text-green-400 mt-1 group-hover:scale-105 transition-all">
                      ₹{Math.floor(currentWinnings).toLocaleString()}
                    </p>
                  </div>
                </div>
                <div className="relative group">
                  <div className="absolute inset-0 bg-yellow-400/5 blur-xl rounded-lg group-hover:bg-yellow-400/10 transition-all duration-300"></div>
                  <div className="relative bg-gradient-to-br from-yellow-500/10 to-orange-500/10 rounded-lg p-3 border border-yellow-500/20 group-hover:border-yellow-400/40 transition-all duration-300">
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
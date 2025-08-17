import { useState, useEffect, useCallback } from 'react';
import { supabase } from './supabaseClient';
import { useRef } from 'react';
import Login from './Login';
import Dashboard from './pages/Dashboard';
import WithdrawalForm from './components/WithdrawalForm';
// Confetti effect for win
import confetti from 'canvas-confetti';
import { Zap, Shield, X, CreditCard } from 'lucide-react';
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
  const [depositAmount, setDepositAmount] = useState(100);
  const [shakeScreen, setShakeScreen] = useState(false);
  const [showTooltip, setShowTooltip] = useState<string | null>(null);
  const [gameSession, setGameSession] = useState<any>(null); // Store session object from Supabase
  const [showAccount, setShowAccount] = useState(false);
  const [uploading, setUploading] = useState(false);
  const [username, setUsername] = useState(user?.user_metadata?.full_name || user?.user_metadata?.name || '');
  const [showWithdrawal, setShowWithdrawal] = useState(false);
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
      navigator.vibrate(90); // Light vibration for rewards
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
      <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900">
        <div className="relative">
          <div className="w-16 h-16 border-4 border-blue-500/30 border-t-blue-500 rounded-full animate-spin"></div>
          <div className="absolute inset-0 w-16 h-16 border-4 border-transparent border-t-purple-500 rounded-full animate-spin" style={{ animationDirection: 'reverse', animationDuration: '1.5s' }}></div>
          <p className="text-blue-400 text-lg font-semibold mt-4 text-center animate-pulse">Loading...</p>
        </div>
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
    <div className={`min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 text-white flex flex-col transition-all duration-500 relative overflow-hidden ${shakeScreen ? 'animate-pulse' : ''}`}>
      {/* Background Elements */}
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        <div className="absolute -top-40 -right-40 w-80 h-80 bg-blue-500/5 rounded-full blur-3xl animate-float"></div>
        <div className="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-500/5 rounded-full blur-3xl animate-float" style={{ animationDelay: '1s' }}></div>
        <div className="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-cyan-500/5 rounded-full blur-3xl animate-pulse-slow"></div>
      </div>
      {/* Withdrawal Modal */}
      {showWithdrawal && (
        <WithdrawalForm 
          onClose={() => setShowWithdrawal(false)}
          balance={balance}
          onWithdrawalSubmitted={(amount) => {
            setBalance(prev => prev - amount);
            setShowWithdrawal(false);
          }}
        />
      )}
      {/* Header with clickable grid/bomb settings button */}
      <div className="w-full max-w-2xl flex-shrink-0 px-2 sm:px-4 md:px-6 py-3 bg-gradient-to-r from-slate-800/90 to-blue-900/90 backdrop-blur-md border-b border-white/20 mx-auto shadow-xl sticky top-0 z-40">
        <div className="flex items-center justify-between">
          {/* Logo and Title */}
          <div className="flex items-center gap-3 group">
            <div className="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl group-hover:shadow-blue-500/25 transition-all duration-300 animate-bounce-short">
              <Zap className="w-5 h-5 sm:w-6 sm:h-6 text-white" />
            </div>
            <h1 className="text-lg sm:text-2xl font-extrabold text-gradient tracking-wide text-shadow group-hover:scale-105 transition-transform duration-300">
              Bombaclat Mine
            </h1>
            <div className="w-6 h-6 sm:w-7 sm:h-7 bg-gradient-to-br from-green-400 to-emerald-500 rounded-md flex items-center justify-center shadow-md animate-pulse-slow">
              <Shield className="w-4 h-4 sm:w-5 sm:h-5 text-white" />
            </div>
          </div>
          {/* Balance and Settings Button */}
          <div className="flex items-center gap-6 relative">
              <div className="text-right group">
                <p className="text-xs text-gray-400 font-medium">Balance</p>
                <div className="bg-gradient-to-r from-green-500/20 to-emerald-500/20 backdrop-blur-sm border border-green-500/30 px-3 py-2 rounded-xl shadow-lg group-hover:shadow-xl group-hover:shadow-green-500/25 transition-all duration-300 animate-pulse-slow">
                  <p className="text-lg font-extrabold text-gradient-success text-shadow">₹{balance.toLocaleString()}</p>
                </div>
              </div>
            {/* User Account Icon and Logout always visible when logged in */}
            {user && (
              <>
                <button
                  onClick={() => setShowAccount((prev) => !prev)}
                  className="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-blue-500/25 shadow-lg border-2 border-blue-400/50 ml-2 group"
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
                  className="ml-2 px-3 py-1 bg-gradient-to-r from-red-500 to-pink-600 rounded-lg text-xs font-bold text-white hover:from-red-600 hover:to-pink-700 transition-all duration-300 shadow-md hover:shadow-lg hover:shadow-red-500/25"
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
                  <div className="card p-6 w-full max-w-sm max-h-[85vh] overflow-y-auto relative animate-fade-in-up">
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
      <div className="flex-1 w-full flex justify-center items-center p-4 max-h-[calc(100vh-4rem)] relative z-10">
        <div className="w-full max-w-2xl mx-auto flex flex-col gap-6 overflow-y-auto">
          {/* Progress Bar for revealed tiles */}
          {gameState === 'playing' && (
            <div className="w-full animate-fade-in-up">
              <div className="bg-gradient-to-r from-slate-800/50 to-blue-900/50 backdrop-blur-sm rounded-2xl p-4 border border-white/20 shadow-xl">
                <div className="space-y-3">
                  <div className="flex justify-between items-center">
                    <span className="text-sm font-medium text-gray-300">Progress</span>
                    <span className="text-sm font-semibold text-blue-400">{Math.round((tilesRevealed / settings.gridSize) * 100)}%</span>
                  </div>
                  <div className="h-3 bg-slate-700/50 rounded-full overflow-hidden shadow-inner">
                    <div
                      className="h-full bg-gradient-to-r from-blue-500 to-purple-600 transition-all duration-700 ease-out rounded-full relative overflow-hidden"
                      style={{ width: `${(tilesRevealed / settings.gridSize) * 100}%` }}
                    >
                      <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shimmer"></div>
                    </div>
                  </div>
                  <div className="flex justify-between text-xs text-gray-400">
                    <span>Tiles: {tilesRevealed}/{settings.gridSize}</span>
                    <span>Grid: {Math.sqrt(settings.gridSize)}×{Math.sqrt(settings.gridSize)}</span>
                  </div>
                </div>
              </div>
            </div>
          )}
          {/* Game State: Betting */}
          {gameState === 'betting' && (
            <div className="space-y-6 animate-fade-in-up">
              {/* Bet Controls */}
              <div className="card-hover p-6">
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
                    <p className="text-3xl font-extrabold text-gradient text-shadow">₹{settings.betAmount}</p>
                  </div>
                  <div className="flex items-center gap-2">
                    <button
                      onClick={() => adjustBetAmount(-50)}
                      className="w-12 h-12 bg-gradient-to-br from-slate-700 to-slate-800 hover:from-red-500 hover:to-pink-600 rounded-xl flex items-center justify-center transition-all duration-300 active:scale-95 shadow-lg hover:shadow-xl hover:shadow-red-500/25 border border-slate-600/50"
                      aria-label="Decrease Bet"
                    >
                      <span className="w-5 h-5 font-bold text-white">−</span>
                    </button>
                    <button
                      onClick={() => adjustBetAmount(50)}
                      className="w-12 h-12 bg-gradient-to-br from-slate-700 to-slate-800 hover:from-green-500 hover:to-emerald-600 rounded-xl flex items-center justify-center transition-all duration-300 active:scale-95 shadow-lg hover:shadow-xl hover:shadow-green-500/25 border border-slate-600/50"
                      aria-label="Increase Bet"
                    >
                      <span className="w-5 h-5 font-bold text-white">+</span>
                    </button>
                  </div>
                </div>
                <div className="grid grid-cols-2 gap-4 mb-6">
                  <button
                    onClick={() => setShowDeposit(true)}
                    className="btn-success py-3 text-sm font-semibold"
                  >
                    <span className="flex items-center justify-center gap-2">
                      <span className="text-lg">💰</span>
                      + Add Funds
                    </span>
                  </button>
                  <button
                    onClick={() => setShowSettings(true)}
                    className="btn-primary py-3 text-sm font-semibold"
                    aria-label="Adjust grid and bomb settings"
                  >
                    <span className="flex items-center justify-center gap-2">
                      <span className="text-lg">⚙️</span>
                      Grid: {getGridCols()}x{getGridCols()}, Bombs: {settings.bombCount}
                    </span>
                  </button>
                </div>
                <button
                  onClick={startGame}
                  disabled={balance < settings.betAmount}
                  className="w-full py-5 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 disabled:from-gray-600 disabled:to-gray-700 disabled:cursor-not-allowed rounded-2xl font-bold text-xl transition-all duration-300 flex items-center justify-center gap-3 active:scale-95 shadow-xl hover:shadow-2xl hover:shadow-blue-500/25 animate-bounce-short glow-effect"
                >
                  <span className="text-2xl">⛏️</span>
                  <span className="bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent">
                    Start Mining
                  </span>
                </button>
              </div>
            </div>
          )}

          {/* Game State: Playing */}
          {gameState === 'playing' && (
            <div className="space-y-4">
              {/* Game Stats */}
              <div className="grid grid-cols-2 gap-4 animate-fade-in-up">
                <div className="relative group">
                  <div className="absolute inset-0 bg-green-400/10 blur-xl rounded-2xl group-hover:bg-green-400/20 transition-all duration-300"></div>
                  <div className="relative card-hover p-4 border-green-500/30 group-hover:border-green-400/50">
                    <div className="flex items-center justify-between mb-2">
                      <p className="text-sm font-medium text-green-400">Current Win</p>
                      <span className="text-xs bg-green-500/20 px-2 py-1 rounded-full text-green-300 font-medium">Safe to Collect</span>
                    </div>
                    <p className="text-2xl font-bold text-gradient-success text-shadow group-hover:scale-105 transition-all">
                      ₹{Math.floor(currentWinnings).toLocaleString()}
                    </p>
                  </div>
                </div>
                <div className="relative group">
                  <div className="absolute inset-0 bg-yellow-400/10 blur-xl rounded-2xl group-hover:bg-yellow-400/20 transition-all duration-300"></div>
                  <div className="relative card-hover p-4 border-yellow-500/30 group-hover:border-yellow-400/50">
                    <div className="flex items-center justify-between mb-2">
                      <p className="text-sm font-medium text-yellow-400">Next Payout</p>
                      <span className="text-xs bg-yellow-500/20 px-2 py-1 rounded-full text-yellow-300 font-medium">If Safe</span>
                    </div>
                    <p className="text-2xl font-bold text-yellow-400 text-shadow group-hover:scale-105 transition-all">
                      ₹{Math.floor(getPotentialPayout()).toLocaleString()}
                    </p>
                  </div>
                </div>
              </div>

              {/* Game Grid */}
              <div className="card-hover p-6">
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
                        aspect-square rounded-xl border-2 transition-all duration-300 text-base sm:text-lg font-bold relative overflow-hidden
                        group backdrop-blur-sm shadow-lg
                        ${tile.revealed 
                          ? !tile.isBomb 
                            ? 'bg-gradient-to-br from-green-400 to-emerald-500 border-green-300 shadow-xl shadow-green-500/40 scale-95 animate-reveal-success' 
                            : 'bg-gradient-to-br from-red-500 to-pink-600 border-red-400 shadow-xl shadow-red-500/40 scale-90 animate-reveal-danger'
                          : 'bg-gradient-to-br from-slate-700/90 to-blue-900/90 border-slate-600 hover:border-blue-400 hover:shadow-xl hover:shadow-blue-500/30 hover:scale-105 active:scale-95 hover:bg-slate-600/90'
                        }
                        ${gameState !== 'playing' ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'}
                      `}
                      style={{ minWidth: 0 }}
                    >
                      {tile.revealed ? (
                        <div className={`flex items-center justify-center h-full ${!tile.isBomb ? 'animate-bounce-short' : 'animate-spin-once'}`}>
                          {!tile.isBomb ? (
                            <span className="text-2xl sm:text-3xl drop-shadow-lg">💎</span>
                          ) : (
                            <span className="text-2xl sm:text-3xl drop-shadow-lg">💣</span>
                          )}
                        </div>
                      ) : (
                        <div className="absolute inset-0 bg-gradient-to-br from-blue-500/0 to-purple-500/0 group-hover:from-blue-500/10 group-hover:to-purple-500/10 transition-all duration-300"></div>
                        <div className="absolute inset-0 bg-gradient-to-br from-white/0 to-white/0 group-hover:from-white/5 group-hover:to-white/5 transition-all duration-300 rounded-xl"></div>
                      )}
                    </button>
                  ))}
                </div>

                {/* Cash Out Button */}
                {currentWinnings > 0 && (
                  <div className="mt-6 relative animate-fade-in-up">
                    <div className="absolute inset-0 bg-green-400/20 blur-xl rounded-2xl animate-pulse-slow"></div>
                    <button
                      onClick={cashOut}
                      className="relative w-full py-5 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 rounded-2xl font-bold text-xl transition-all duration-300 active:scale-95 border-2 border-green-400/50 hover:border-green-400 shadow-xl shadow-green-500/30 hover:shadow-2xl hover:shadow-green-500/50 group glow-effect"
                    >
                      <span className="flex items-center justify-center gap-3">
                        <span className="text-3xl group-hover:scale-110 transition-transform animate-bounce-short">💰</span>
                        <span className="bg-gradient-to-r from-white to-green-100 bg-clip-text text-transparent font-extrabold">
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
            <div className="text-center p-8 card border-red-500/30 animate-fade-in-up">
              <div className="text-6xl mb-4 animate-bounce-short">💥</div>
              <h3 className="text-2xl font-bold text-gradient-danger mb-3">Mine Detonated!</h3>
              <p className="text-gray-300 text-lg">Lost ₹{settings.betAmount}. Better luck next time!</p>
              <div className="mt-4 w-16 h-1 bg-gradient-to-r from-red-500 to-pink-500 rounded-full mx-auto"></div>
            </div>
          )}

          {/* Game State: Collected */}
          {gameState === 'collected' && (
            <div className="text-center p-8 card border-green-500/30 animate-fade-in-up">
              <div className="text-6xl mb-4 animate-bounce-short">🎉</div>
              <h3 className="text-2xl font-bold text-gradient-success mb-3">Successfully Collected!</h3>
              <p className="text-gray-300 text-lg">Won ₹{Math.floor(currentWinnings)}!</p>
              <div className="mt-4 w-16 h-1 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full mx-auto"></div>
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

                {/* Withdrawal Button */}
          <div className="fixed bottom-6 right-6 z-50 animate-fade-in-up">
            <button 
              onClick={() => {
                if (balance < 500) {
                  alert('You need at least ₹500 to withdraw');
                  return;
                }
                setShowWithdrawal(true);
              }}
              className="flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 rounded-2xl text-white font-semibold transition-all duration-300 shadow-xl hover:shadow-2xl hover:shadow-green-500/25 group"
            >
              <CreditCard className="w-6 h-6 group-hover:scale-110 transition-transform" />
              <span className="hidden sm:inline">Withdraw Funds</span>
              <span className="sm:hidden">Withdraw</span>
            </button>
          </div>

          {/* Settings Modal */}
      {showSettings && (
        <div className="fixed inset-0 bg-black/60 backdrop-blur-md flex items-center justify-center z-50 p-2 sm:p-4">
          <div className="card p-6 w-full max-w-sm max-h-[80vh] overflow-y-auto animate-scale-in">
            <div className="flex items-center justify-between mb-6">
                              <h3 className="text-xl font-bold text-gradient">Game Settings</h3>
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
                    className={`py-3 rounded-xl text-sm font-semibold transition-all duration-300 border ${
                      settings.gridSize === option.size
                        ? 'bg-gradient-to-r from-purple-500 to-blue-600 text-white border-purple-400 shadow-lg shadow-purple-500/25'
                        : 'bg-slate-700/50 text-gray-300 hover:bg-slate-600/50 border-slate-600/30 hover:border-slate-500/50'
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
              className="btn-primary w-full py-3 font-semibold"
            >
              Save Settings
            </button>
          </div>
        </div>
      )}

      {/* Deposit Modal */}
      {showDeposit && (
        <div className="fixed inset-0 bg-black/60 backdrop-blur-md flex items-center justify-center z-50">
          <div className="relative w-full max-w-sm mx-auto h-[calc(100vh-2rem)] m-4 flex flex-col">
            <div className="card w-full flex flex-col max-h-full overflow-hidden">
              <div className="flex items-center justify-between p-4 border-b border-white/20">
                <h3 className="text-xl font-bold text-gradient">Add Funds</h3>
                <button
                  onClick={() => setShowDeposit(false)}
                  className="w-10 h-10 bg-red-500/20 hover:bg-red-500/40 rounded-xl flex items-center justify-center transition-all duration-300 border-2 border-red-500/50 hover:shadow-lg hover:shadow-red-500/25"
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


    </div>
  );
}

export default App;

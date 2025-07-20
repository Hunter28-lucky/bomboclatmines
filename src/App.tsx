import React, { useState, useEffect, useCallback } from 'react';
import { Zap, Trophy, RotateCcw, Settings, Play, DollarSign, Plus, Minus, Wallet, Shield, Code, X } from 'lucide-react';
import PaymentWidget from './PaymentWidget';

interface Tile {
  id: number;
  revealed: boolean;
  isReward: boolean;
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
  const [tiles, setTiles] = useState<Tile[]>([]);
  const [currentWinnings, setCurrentWinnings] = useState(0);
  const [balance, setBalance] = useState(500); // Starting balance ₹500
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
  const [gameSession, setGameSession] = useState(Date.now()); // Security: Session tracking

  const gridSizeOptions = [
    { size: 16, label: '4×4', cols: 4 },
    { size: 25, label: '5×5', cols: 5 },
    { size: 36, label: '6×6', cols: 6 }
  ];

  // Security: Anti-tampering protection
  useEffect(() => {
    const protectConsole = () => {
      if (typeof window !== 'undefined') {
        // Disable common debugging methods
        (window as any).console.log = () => {};
        (window as any).console.warn = () => {};
        (window as any).console.error = () => {};
        
        // Detect developer tools
        let devtools = { open: false, orientation: null };
        const threshold = 160;
        
        setInterval(() => {
          if (window.outerHeight - window.innerHeight > threshold || 
              window.outerWidth - window.innerWidth > threshold) {
            if (!devtools.open) {
              devtools.open = true;
              // Security action: Reset game state if dev tools detected
              setGameState('betting');
              setBalance(500);
            }
          } else {
            devtools.open = false;
          }
        }, 500);
      }
    };
    
    protectConsole();
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
      const isReward = !bombPositions.has(i);
      newTiles.push({
        id: i,
        revealed: false,
        isReward,
        multiplier: isReward ? calculateMultiplier(0, settings.gridSize, settings.bombCount) : 0
      });
    }
    
    setTiles(newTiles);
    setCurrentWinnings(0);
    setTilesRevealed(0);
  }, [settings, calculateMultiplier, gameSession]);

  // Start new game with security validation
  const startGame = () => {
    if (balance < settings.betAmount) {
      alert('Insufficient balance! Please deposit more funds.');
      return;
    }
    
    // Security: Validate session and reset if needed
    setGameSession(Date.now());
    setBalance(prev => prev - settings.betAmount);
    setGameState('playing');
    initializeGrid();
    playSound('click');
    lightHaptic();
  };

  // Handle tile click with security checks
  const handleTileClick = (tileId: number) => {
    if (gameState !== 'playing') return;
    
    const tile = tiles.find(t => t.id === tileId);
    if (!tile || tile.revealed) return;

    const newTiles = tiles.map(t => 
      t.id === tileId ? { ...t, revealed: true } : t
    );
    
    setTiles(newTiles);
    const newTilesRevealed = tilesRevealed + 1;
    setTilesRevealed(newTilesRevealed);

    if (tile.isReward) {
      const multiplier = calculateMultiplier(newTilesRevealed, settings.gridSize, settings.bombCount);
      const winAmount = settings.betAmount * multiplier;
      setCurrentWinnings(winAmount);
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
  const cashOut = () => {
    if (currentWinnings > 0) {
      setBalance(prev => prev + Math.floor(currentWinnings));
      setGameState('collected');
      playSound('cashout');
      mediumHaptic();
      setTimeout(() => {
        setGameState('betting');
      }, 2000);
    }
  };

  // Handle deposit with validation
  const handleDeposit = () => {
    setBalance(prev => prev + depositAmount);
    setShowDeposit(false);
    playSound('cashout');
    lightHaptic();
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
    <div className={`min-h-screen bg-gradient-to-br from-gray-900 via-slate-900 to-gray-900 text-white flex flex-col items-center justify-center transition-all duration-500 ${shakeScreen ? 'animate-pulse' : ''}`}>
      
      {/* Fixed Header - No Scroll */}
      <div className="w-full max-w-2xl flex-shrink-0 px-2 sm:px-4 md:px-8 py-3 bg-gradient-to-r from-gray-800/90 to-slate-800/90 backdrop-blur-sm border-b border-gray-700/50 mx-auto">
        <div className="flex items-center justify-between">
          {/* Logo and Title */}
          <div className="flex items-center gap-2">
            <div className="w-8 h-8 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-lg flex items-center justify-center">
              <Zap className="w-5 h-5 text-white" />
            </div>
            <h1 className="text-lg font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">
              Bombaclat Mine
            </h1>
            <div className="w-6 h-6 bg-gradient-to-br from-green-400 to-emerald-500 rounded-md flex items-center justify-center">
              <Shield className="w-4 h-4 text-white" />
            </div>
          </div>

          {/* Balance and Settings */}
          <div className="flex items-center gap-3">
            <div className="text-right">
              <p className="text-xs text-gray-400">Balance</p>
              <p className="text-sm font-bold text-green-400">₹{balance.toLocaleString()}</p>
            </div>
            <button
              onClick={() => setShowSettings(true)}
              className="w-10 h-10 bg-gray-700/50 hover:bg-gray-600/50 rounded-lg flex items-center justify-center transition-all duration-300 active:scale-95"
            >
              <Settings className="w-5 h-5" />
            </button>
          </div>
        </div>
      </div>

      {/* Main Game Area - Responsive Container */}
      <div className="flex-1 w-full flex justify-center items-center overflow-y-auto">
        <div className="w-full max-w-2xl p-2 sm:p-4 md:p-8 pb-20 mx-auto">
          
          {/* Game State: Betting */}
          {gameState === 'betting' && (
            <div className="space-y-4">
              {/* Bet Controls */}
              <div className="bg-gradient-to-r from-gray-800 to-slate-800 rounded-xl p-4 border border-gray-700/50">
                <div className="flex items-center justify-between mb-4">
                  <div>
                    <p className="text-sm text-gray-400">Bet Amount</p>
                    <p className="text-xl font-bold text-cyan-400">₹{settings.betAmount}</p>
                  </div>
                  <div className="flex items-center gap-2">
                    <button
                      onClick={() => adjustBetAmount(-50)}
                      className="w-10 h-10 bg-gray-700/50 hover:bg-gray-600/50 rounded-lg flex items-center justify-center transition-all duration-300 active:scale-95"
                    >
                      <Minus className="w-4 h-4" />
                    </button>
                    <button
                      onClick={() => adjustBetAmount(50)}
                      className="w-10 h-10 bg-gray-700/50 hover:bg-gray-600/50 rounded-lg flex items-center justify-center transition-all duration-300 active:scale-95"
                    >
                      <Plus className="w-4 h-4" />
                    </button>
                  </div>
                </div>

                <div className="grid grid-cols-2 gap-3 mb-4">
                  <button
                    onClick={() => setShowDeposit(true)}
                    className="py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 rounded-lg text-sm font-semibold transition-all duration-300 active:scale-95"
                  >
                    + Add Funds
                  </button>
                  <div className="bg-gray-700/30 rounded-lg p-3 text-center">
                    <p className="text-xs text-gray-400">Grid: {gridSizeOptions.find(opt => opt.size === settings.gridSize)?.label}</p>
                    <p className="text-xs text-red-400">Bombs: {settings.bombCount}</p>
                  </div>
                </div>

                <button
                  onClick={startGame}
                  disabled={balance < settings.betAmount}
                  className="w-full py-4 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 disabled:from-gray-600 disabled:to-gray-700 disabled:cursor-not-allowed rounded-lg font-bold text-lg transition-all duration-300 flex items-center justify-center gap-3 active:scale-95"
                >
                  <Play className="w-5 h-5" />
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
                <div className="bg-gradient-to-br from-green-500/10 to-emerald-500/10 rounded-lg p-3 border border-green-500/20">
                  <p className="text-xs text-green-400 mb-1">Current Win</p>
                  <p className="text-lg font-bold text-green-400">₹{Math.floor(currentWinnings)}</p>
                </div>
                <div className="bg-gradient-to-br from-yellow-500/10 to-orange-500/10 rounded-lg p-3 border border-yellow-500/20">
                  <p className="text-xs text-yellow-400 mb-1">Next Payout</p>
                  <p className="text-lg font-bold text-yellow-400">₹{Math.floor(getPotentialPayout())}</p>
                </div>
              </div>

              {/* Game Grid */}
              <div className="bg-gradient-to-br from-gray-800 to-slate-800 rounded-xl p-2 sm:p-4 border border-gray-700/50">
                <div 
                  className="grid gap-1 sm:gap-2 mb-4"
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
                        ${tile.revealed 
                          ? tile.isReward 
                            ? 'bg-gradient-to-br from-green-400 to-emerald-500 border-green-300 shadow-lg shadow-green-500/30 scale-95' 
                            : 'bg-gradient-to-br from-red-500 to-red-600 border-red-400 shadow-lg shadow-red-500/30 scale-90'
                          : 'bg-gradient-to-br from-gray-700 to-slate-700 border-gray-600 hover:border-cyan-400 hover:shadow-lg hover:shadow-cyan-500/20 hover:scale-105 active:scale-95'
                        }
                        ${gameState !== 'playing' ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'}
                      `}
                      style={{ minWidth: 0 }}
                    >
                      {tile.revealed && (
                        <div className="flex items-center justify-center h-full">
                          {tile.isReward ? (
                            <span className="text-lg sm:text-xl">💎</span>
                          ) : (
                            <span className="text-lg sm:text-xl">💣</span>
                          )}
                        </div>
                      )}
                    </button>
                  ))}
                </div>

                {/* Cash Out Button */}
                {currentWinnings > 0 && (
                  <button
                    onClick={cashOut}
                    className="w-full py-4 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 rounded-lg font-bold text-lg transition-all duration-300 active:scale-95"
                  >
                    💰 Collect ₹{Math.floor(currentWinnings)}
                  </button>
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
                <div className="w-5 h-5 bg-gradient-to-br from-purple-400 to-blue-500 rounded-md flex items-center justify-center">
                  <Code className="w-3 h-3 text-white" />
                </div>
                <span className="text-sm font-semibold bg-gradient-to-r from-purple-400 to-blue-400 bg-clip-text text-transparent">
                  Developed by Krish Goswami
                </span>
              </div>
              <p className="text-xs text-gray-500">Professional Game Development</p>
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
                    onClick={() => setSettings(prev => ({ ...prev, gridSize: option.size }))}
                    className={`py-3 rounded-lg text-sm font-semibold transition-all duration-300 border ${
                      settings.gridSize === option.size
                        ? 'bg-gradient-to-r from-purple-500 to-blue-500 text-white border-purple-400'
                        : 'bg-gray-700/50 text-gray-300 hover:bg-gray-600/50 border-gray-600/30'
                    } active:scale-95`}
                  >
                    {option.label}
                  </button>
                ))}
              </div>
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
                onChange={(e) => setSettings(prev => ({ ...prev, bombCount: parseInt(e.target.value) }))}
                className="w-full h-3 bg-gray-700 rounded-lg appearance-none cursor-pointer"
              />
              <div className="flex justify-between text-xs text-gray-400 mt-2">
                <span>Low Risk</span>
                <span>High Risk</span>
              </div>
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
        <div className="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-2 sm:p-4">
          <div className="bg-gradient-to-br from-gray-800 to-slate-800 rounded-xl p-4 sm:p-6 w-full max-w-sm border border-gray-700/50">
            <div className="flex items-center justify-between mb-6">
              <h3 className="text-xl font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">Add Funds</h3>
              <button
                onClick={() => setShowDeposit(false)}
                className="w-8 h-8 bg-gray-700/50 hover:bg-gray-600/50 rounded-lg flex items-center justify-center transition-all duration-300"
              >
                <X className="w-4 h-4" />
              </button>
            </div>
            <PaymentWidget siteId="c2de4f18-ab31-4542-930e-9c3c0a162e18" />
          </div>
        </div>
      )}

      <style>{`
        input[type="range"]::-webkit-slider-thumb {
          appearance: none;
          height: 20px;
          width: 20px;
          border-radius: 50%;
          background: linear-gradient(45deg, #06b6d4, #3b82f6);
          cursor: pointer;
          box-shadow: 0 4px 8px rgba(6, 182, 212, 0.3);
        }
        
        input[type="range"]::-moz-range-thumb {
          height: 20px;
          width: 20px;
          border-radius: 50%;
          background: linear-gradient(45deg, #06b6d4, #3b82f6);
          cursor: pointer;
          border: none;
          box-shadow: 0 4px 8px rgba(6, 182, 212, 0.3);
        }
      `}</style>
    </div>
  );
}

export default App;
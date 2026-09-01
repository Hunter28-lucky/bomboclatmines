// Bomboclat Mines - Obsidian Operator Studio Controller

document.addEventListener('DOMContentLoaded', () => {
  const state = {
    stats: {},
    users: [],
    settings: {},
    activeTab: 'players'
  };

  // DOM Elements
  const el = {
    kpiUsers: document.getElementById('kpi-total-users'),
    kpiBalance: document.getElementById('kpi-total-balance'),
    kpiGames: document.getElementById('kpi-total-games'),
    kpiRtp: document.getElementById('kpi-global-rtp'),
    kpiHouseEdge: document.getElementById('kpi-house-edge'),
    kpiPending: document.getElementById('kpi-pending-count'),
    // RTP slider
    rtpSlider: document.getElementById('rtp-range-slider'),
    rtpSliderVal: document.getElementById('rtp-slider-val'),
    houseEdgeDisplay: document.getElementById('house-edge-display'),
    btnSaveRtp: document.getElementById('btn-save-rtp'),
    // Settings form
    settingsForm: document.getElementById('system-settings-form'),
    // Search
    userSearchInput: document.getElementById('user-search-input'),
    // Tables
    playersTbody: document.getElementById('players-tbody'),
    depositsTbody: document.getElementById('deposits-tbody'),
    withdrawalsTbody: document.getElementById('withdrawals-tbody'),
    // Tabs
    tabBtns: document.querySelectorAll('.tab-bar-btn'),
    tabPlayers: document.getElementById('tab-content-players'),
    tabDeposits: document.getElementById('tab-content-deposits'),
    tabWithdrawals: document.getElementById('tab-content-withdrawals'),
    tabSupport: document.getElementById('tab-content-support'),
    supportThreadsList: document.getElementById('admin-support-threads-list'),
    supportMessagesContainer: document.getElementById('admin-chat-messages-container'),
    supportReplyForm: document.getElementById('admin-support-reply-form'),
    supportReplyInput: document.getElementById('admin-reply-input'),
    supportReplyUserId: document.getElementById('admin-reply-user-id'),
    supportChatUserName: document.getElementById('admin-chat-user-name'),
    supportChatUserMeta: document.getElementById('admin-chat-user-meta'),
    btnRefreshSupport: document.getElementById('btn-refresh-admin-support'),
    // Manage Modal
    manageModal: document.getElementById('player-manage-modal'),
    modalUserId: document.getElementById('modal-user-id'),
    modalUserName: document.getElementById('modal-user-name'),
    modalUserEmail: document.getElementById('modal-user-email'),
    modalUserRig: document.getElementById('modal-user-rig'),
    modalUserRtp: document.getElementById('modal-user-rtp'),
    modalUserBalance: document.getElementById('modal-user-balance'),
    modalUserNewPass: document.getElementById('modal-user-new-password'),
    // Add Money Modal
    addMoneyModal: document.getElementById('add-money-modal'),
    addMoneyUserId: document.getElementById('add-money-user-id'),
    addMoneyUserEmail: document.getElementById('add-money-user-email'),
    addMoneyAmountInput: document.getElementById('add-money-amount-input'),
    btnConfirmAddMoney: document.getElementById('btn-confirm-add-money'),
    // Deduct Money Modal
    deductMoneyModal: document.getElementById('deduct-money-modal'),
    deductMoneyUserId: document.getElementById('deduct-money-user-id'),
    deductMoneyUserEmail: document.getElementById('deduct-money-user-email'),
    deductMoneyAmountInput: document.getElementById('deduct-money-amount-input'),
    btnConfirmDeductMoney: document.getElementById('btn-confirm-deduct-money')
  };

  // Fetch KPI Stats
  async function fetchStats() {
    try {
      const res = await fetch('api/admin.php?action=stats');
      const data = await res.json();
      if (data.success && data.stats) {
        state.stats = data.stats;
        if (el.kpiUsers) el.kpiUsers.textContent = data.stats.total_users;
        if (el.kpiBalance) el.kpiBalance.textContent = '₹' + Number(data.stats.total_balance).toLocaleString('en-IN', { maximumFractionDigits: 0 });
        if (el.kpiGames) el.kpiGames.textContent = data.stats.total_games;
        if (el.kpiRtp) el.kpiRtp.textContent = Number(data.stats.rtp_rate).toFixed(1) + '%';
        if (el.kpiHouseEdge) el.kpiHouseEdge.textContent = `House Edge: ${Number(data.stats.house_edge).toFixed(1)}%`;
        if (el.kpiPending) el.kpiPending.textContent = (data.stats.pending_payments + data.stats.pending_withdrawals);

        if (el.rtpSlider && data.stats.rtp_rate) {
          el.rtpSlider.value = data.stats.rtp_rate;
          el.rtpSliderVal.textContent = Number(data.stats.rtp_rate).toFixed(1) + '%';
          if (el.houseEdgeDisplay) el.houseEdgeDisplay.textContent = Number(data.stats.house_edge).toFixed(1) + '%';
        }
      }
    } catch (e) {
      console.error('Failed to load stats', e);
    }
  }

  // Fetch System Settings
  async function fetchSettings() {
    try {
      const res = await fetch('api/admin.php?action=get_settings');
      const data = await res.json();
      if (data.success && data.settings) {
        state.settings = data.settings;
        const setVal = (id, val) => {
          const input = document.getElementById(id);
          if (input && val !== undefined) input.value = val;
        };
        setVal('setting-min-bet', data.settings.min_bet);
        setVal('setting-max-bet', data.settings.max_bet);
        setVal('setting-min-withdrawal', data.settings.min_withdrawal);
        setVal('setting-welcome-bonus', data.settings.welcome_bonus);
        setVal('setting-upi-id', data.settings.upi_id);
      }
    } catch (e) {
      console.error('Failed to load settings', e);
    }
  }

  // Fetch Players
  async function fetchUsers(search = '') {
    try {
      const url = `api/admin.php?action=users${search ? '&search=' + encodeURIComponent(search) : ''}`;
      const res = await fetch(url);
      const data = await res.json();
      if (data.success && data.users) {
        state.users = data.users;
        renderUsers(data.users);
      }
    } catch (e) {
      console.error('Failed to load users', e);
    }
  }

  // Render Players Table
  function renderUsers(users) {
    if (!el.playersTbody) return;
    el.playersTbody.innerHTML = '';

    if (users.length === 0) {
      el.playersTbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding:20px; color:#64748b;">No players found</td></tr>';
      return;
    }

    users.forEach(u => {
      const tr = document.createElement('tr');
      const isBanned = u.is_banned === 1;
      const profitColor = u.net_profit >= 0 ? 'var(--accent-emerald)' : '#ef4444';
      const profitSign = u.net_profit >= 0 ? '+₹' : '-₹';
      const formattedProfit = profitSign + Math.abs(u.net_profit).toFixed(2);

      let rigBadgeClass = '';
      if (u.rig_mode === 'force_lose') rigBadgeClass = 'trap';
      if (u.rig_mode === 'force_win') rigBadgeClass = 'lucky';

      const isPromoter = u.is_promoter === 1;

      tr.innerHTML = `
        <td>
          <div class="player-cell-box">
            <div style="display:flex; align-items:center; gap:6px;">
              <span class="player-name">${escapeHtml(u.full_name || 'Player')}</span>
              ${isPromoter ? '<span style="background:linear-gradient(135deg,#eab308,#ca8a04); color:#000; font-size:0.62rem; font-weight:900; padding:1px 6px; border-radius:10px; box-shadow:0 0 8px rgba(234,179,8,0.4);">⭐ PROMOTER</span>' : ''}
            </div>
            <span class="player-email">${escapeHtml(u.email)}</span>
            <span style="font-size:0.65rem; color:#64748b;">ID: ${u.user_id.substring(0, 8)}...</span>
          </div>
        </td>
        <td><strong style="color:var(--accent-cyan);">₹${Number(u.balance).toFixed(2)}</strong></td>
        <td>
          <div style="font-size:0.75rem;">${u.games_played} games</div>
          <div style="font-size:0.68rem; color:#94a3b8;">Won: ₹${Number(u.total_won).toFixed(0)}</div>
        </td>
        <td><strong style="color:${profitColor};">${formattedProfit}</strong></td>
        <td>
          <select class="rig-mode-select ${rigBadgeClass}" data-user-id="${u.user_id}">
            <option value="global" ${u.rig_mode === 'global' ? 'selected' : ''}>🌐 Global Default</option>
            <option value="fair" ${u.rig_mode === 'fair' ? 'selected' : ''}>⚖️ Provably Fair (97%)</option>
            <option value="house_favored" ${u.rig_mode === 'house_favored' ? 'selected' : ''}>💰 House Edge (80%)</option>
            <option value="high_win" ${u.rig_mode === 'high_win' ? 'selected' : ''}>🚀 High Win (110%)</option>
            <option value="force_lose" ${u.rig_mode === 'force_lose' ? 'selected' : ''}>⚡ STRICT TRAP</option>
            <option value="force_win" ${u.rig_mode === 'force_win' ? 'selected' : ''}>🌟 LUCKY STAR</option>
          </select>
          ${u.custom_rtp ? `<div style="font-size:0.68rem; color:var(--accent-purple); margin-top:2px;">Custom: ${u.custom_rtp}%</div>` : ''}
        </td>
        <td>
          ${isBanned 
            ? '<span style="background:rgba(239,68,68,0.2); color:#f87171; padding:2px 6px; border-radius:4px; font-size:0.7rem; font-weight:800;">BANNED</span>' 
            : '<span style="background:rgba(16,185,129,0.15); color:#34d399; padding:2px 6px; border-radius:4px; font-size:0.7rem; font-weight:800;">ACTIVE</span>'}
        </td>
        <td>
          <div class="table-btn-group" style="flex-wrap:wrap; gap:4px;">
            <button class="btn-action-tiny ${isPromoter ? 'gold' : ''} btn-toggle-promoter" data-user-id="${u.user_id}" data-is-promoter="${isPromoter ? 1 : 0}" title="Toggle VIP Promoter Mode">${isPromoter ? '⭐ Promoter ON' : '☆ Make Promoter'}</button>
            <button class="btn-action-tiny btn-manage-user" data-user='${JSON.stringify(u)}'>⚙️ Manage</button>
            <button class="btn-action-tiny green btn-open-add-money" data-user-id="${u.user_id}" data-user-email="${escapeHtml(u.email)}">+₹</button>
            <button class="btn-action-tiny red btn-open-deduct-money" data-user-id="${u.user_id}" data-user-email="${escapeHtml(u.email)}">-₹</button>
          </div>
        </td>
      `;

      el.playersTbody.appendChild(tr);
    });

    // Toggle Promoter Mode
    document.querySelectorAll('.btn-toggle-promoter').forEach(btn => {
      btn.addEventListener('click', async () => {
        const uid = btn.dataset.userId;
        const current = parseInt(btn.dataset.isPromoter) || 0;
        const nextState = current === 1 ? 0 : 1;
        try {
          const res = await fetch('api/admin.php?action=toggle_promoter', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: uid, is_promoter: nextState })
          });
          const data = await res.json();
          if (data.success) {
            alert(data.message);
            fetchUsers(el.userSearchInput ? el.userSearchInput.value : '');
          } else {
            alert(data.error || 'Failed to update promoter mode');
          }
        } catch (e) {
          alert('Network error');
        }
      });
    });

    // Inline rig change
    document.querySelectorAll('.rig-mode-select').forEach(select => {
      select.addEventListener('change', async (e) => {
        const userId = e.target.dataset.userId;
        const newRig = e.target.value;
        try {
          const res = await fetch('api/admin.php?action=update_user_rig', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId, rig_mode: newRig })
          });
          const data = await res.json();
          if (data.success) {
            alert(`Game control updated to: ${newRig}`);
            fetchUsers(el.userSearchInput ? el.userSearchInput.value : '');
          } else {
            alert(data.error || 'Failed to update rig');
          }
        } catch (err) {
          alert('Network error');
        }
      });
    });

    // Manage user modal button
    document.querySelectorAll('.btn-manage-user').forEach(btn => {
      btn.addEventListener('click', () => {
        const userData = JSON.parse(btn.dataset.user);
        openUserManageModal(userData);
      });
    });

    // Open Add Money Modal
    document.querySelectorAll('.btn-open-add-money').forEach(btn => {
      btn.addEventListener('click', () => {
        if (el.addMoneyModal) {
          el.addMoneyUserId.value = btn.dataset.userId;
          el.addMoneyUserEmail.textContent = btn.dataset.userEmail;
          el.addMoneyAmountInput.value = '';
          el.addMoneyModal.classList.add('active');
        }
      });
    });

    // Open Deduct Money Modal
    document.querySelectorAll('.btn-open-deduct-money').forEach(btn => {
      btn.addEventListener('click', () => {
        if (el.deductMoneyModal) {
          el.deductMoneyUserId.value = btn.dataset.userId;
          el.deductMoneyUserEmail.textContent = btn.dataset.userEmail;
          el.deductMoneyAmountInput.value = '';
          el.deductMoneyModal.classList.add('active');
        }
      });
    });
  }

  async function adjustUserBalance(userId, amount, type) {
    const action = type === 'add' ? 'add_money' : 'deduct_money';
    try {
      const res = await fetch(`api/admin.php?action=${action}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: userId, amount: amount })
      });
      const data = await res.json();
      if (data.success) {
        alert(data.message);
        fetchUsers(el.userSearchInput ? el.userSearchInput.value : '');
        fetchStats();
      } else {
        alert(data.error || 'Operation failed');
      }
    } catch (e) {
      alert('Network error');
    }
  }

  // Open User Manage Modal
  function openUserManageModal(u) {
    if (!el.manageModal) return;
    el.modalUserId.value = u.user_id;
    el.modalUserName.textContent = u.full_name || 'Player';
    el.modalUserEmail.textContent = u.email;
    el.modalUserRig.value = u.rig_mode || 'global';
    el.modalUserRtp.value = u.custom_rtp !== null && u.custom_rtp !== undefined ? u.custom_rtp : '';
    el.modalUserBalance.value = u.balance;
    el.manageModal.classList.add('active');
  }

  // Fetch Deposits Queue
  async function fetchDeposits() {
    try {
      const res = await fetch('api/admin.php?action=payments');
      const data = await res.json();
      if (data.success && data.payments && el.depositsTbody) {
        el.depositsTbody.innerHTML = '';
        if (data.payments.length === 0) {
          el.depositsTbody.innerHTML = '<tr><td colspan="8" style="text-align:center; padding:20px; color:#64748b;">No deposits pending</td></tr>';
          return;
        }
        data.payments.forEach(p => {
          const tr = document.createElement('tr');
          const isPending = p.status === 'pending';
          const depositAmt = Number(p.amount || 0);
          tr.innerHTML = `
            <td><strong>${escapeHtml(p.user_email || p.user_id)}</strong></td>
            <td><strong style="color:var(--accent-emerald);">₹${depositAmt.toFixed(2)}</strong></td>
            <td><code>${escapeHtml(p.utr_number)}</code></td>
            <td>${escapeHtml(p.mobile_number || '-')}</td>
            <td>
              ${p.screenshot_url 
                ? `<a href="${p.screenshot_url}" target="_blank" style="color:var(--accent-cyan); font-weight:700;">View Receipt</a>` 
                : '<span style="color:#64748b;">No image</span>'}
            </td>
            <td style="font-size:0.75rem; color:#94a3b8;">${p.created_at}</td>
            <td>
              <span style="font-weight:800; font-size:0.75rem; color:${p.status === 'approved' ? 'var(--accent-emerald)' : (p.status === 'rejected' ? '#ef4444' : 'var(--accent-amber)')};">
                ${p.status.toUpperCase()}
              </span>
            </td>
            <td>
              ${isPending ? `
                <div class="table-btn-group">
                  <button class="btn-action-tiny green btn-approve-deposit" data-id="${p.id}" data-user="${p.user_id}" data-amount="${depositAmt}">Approve (Credit ₹)</button>
                  <button class="btn-action-tiny red btn-reject-deposit" data-id="${p.id}">Reject</button>
                </div>
              ` : '<span style="color:#64748b; font-size:0.75rem;">Processed</span>'}
            </td>
          `;
          el.depositsTbody.appendChild(tr);
        });

        // Deposit actions
        document.querySelectorAll('.btn-approve-deposit').forEach(b => {
          b.addEventListener('click', async () => {
            const defaultAmt = b.dataset.amount && Number(b.dataset.amount) > 0 ? b.dataset.amount : '500';
            const amt = prompt('Confirm amount to credit to player wallet (₹):', defaultAmt);
            if (amt && !isNaN(amt) && Number(amt) > 0) {
              const res = await fetch('api/admin.php?action=approve_payment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ payment_id: b.dataset.id, user_id: b.dataset.user, amount: Number(amt) })
              });
              const data = await res.json();
              if (data.success) {
                alert('Deposit approved and wallet credited successfully!');
                fetchDeposits();
                fetchStats();
              } else {
                alert(data.error || 'Failed to approve deposit');
              }
            }
          });
        });

        document.querySelectorAll('.btn-reject-deposit').forEach(b => {
          b.addEventListener('click', async () => {
            if (confirm('Reject this deposit submission?')) {
              const res = await fetch('api/admin.php?action=reject_payment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ payment_id: b.dataset.id })
              });
              const data = await res.json();
              if (data.success) {
                alert('Deposit rejected');
                fetchDeposits();
                fetchStats();
              } else {
                alert(data.error || 'Failed to reject deposit');
              }
            }
          });
        });
      }
    } catch (e) {
      console.error('Failed to load deposits', e);
    }
  }

  // Fetch Withdrawals Queue
  async function fetchWithdrawals() {
    try {
      const res = await fetch('api/admin.php?action=withdrawals');
      const data = await res.json();
      if (data.success && data.withdrawals && el.withdrawalsTbody) {
        el.withdrawalsTbody.innerHTML = '';
        if (data.withdrawals.length === 0) {
          el.withdrawalsTbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding:20px; color:#64748b;">No withdrawals pending</td></tr>';
          return;
        }
        data.withdrawals.forEach(w => {
          const tr = document.createElement('tr');
          const isPending = w.status === 'pending';
          tr.innerHTML = `
            <td><strong>${escapeHtml(w.user_email || w.user_id)}</strong></td>
            <td><strong style="color:var(--accent-emerald);">₹${Number(w.amount).toFixed(2)}</strong></td>
            <td><code>${escapeHtml(w.upi_id)}</code></td>
            <td>${escapeHtml(w.mobile_number || '-')}</td>
            <td style="font-size:0.75rem; color:#94a3b8;">${w.created_at}</td>
            <td>
              <span style="font-weight:800; font-size:0.75rem; color:${w.status === 'approved' ? 'var(--accent-emerald)' : (w.status === 'rejected' ? '#ef4444' : 'var(--accent-amber)')};">
                ${w.status.toUpperCase()}
              </span>
            </td>
            <td>
              ${isPending ? `
                <div class="table-btn-group">
                  <button class="btn-action-tiny green btn-approve-withdraw" data-id="${w.id}">Approve Paid</button>
                  <button class="btn-action-tiny red btn-reject-withdraw" data-id="${w.id}">Reject (Refund)</button>
                </div>
              ` : '<span style="color:#64748b; font-size:0.75rem;">Processed</span>'}
            </td>
          `;
          el.withdrawalsTbody.appendChild(tr);
        });

        document.querySelectorAll('.btn-approve-withdraw').forEach(b => {
          b.addEventListener('click', async () => {
            if (confirm('Confirm you have sent the UPI payout?')) {
              await fetch('api/admin.php?action=approve_withdrawal', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ withdrawal_id: b.dataset.id })
              });
              fetchWithdrawals();
              fetchStats();
            }
          });
        });

        document.querySelectorAll('.btn-reject-withdraw').forEach(b => {
          b.addEventListener('click', async () => {
            const note = prompt('Reason for rejection (funds will be refunded to player):', 'Invalid UPI ID');
            if (note !== null) {
              await fetch('api/admin.php?action=reject_withdrawal', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ withdrawal_id: b.dataset.id, admin_note: note })
              });
              fetchWithdrawals();
              fetchStats();
            }
          });
        });
      }
    } catch (e) {
      console.error('Failed to load withdrawals', e);
    }
  }

  // Fetch Support Tickets & Escalations
  async function fetchSupportTickets(selectedUid = null) {
    try {
      const url = 'api/admin.php?action=support_tickets' + (selectedUid ? `&user_id=${encodeURIComponent(selectedUid)}` : '');
      const res = await fetch(url);
      const data = await res.json();
      if (data.success) {
        renderSupportThreads(data.threads, data.selected_user_id);
        renderActiveSupportThread(data.active_thread, data.threads, data.selected_user_id);
      }
    } catch (e) {
      console.error('Failed to load support tickets', e);
    }
  }

  function renderSupportThreads(threads, selectedUid) {
    if (!el.supportThreadsList) return;
    if (!threads || threads.length === 0) {
      el.supportThreadsList.innerHTML = '<div style="text-align:center; color:#64748b; padding:20px;">No support conversations yet</div>';
      return;
    }

    el.supportThreadsList.innerHTML = threads.map(t => {
      const isSelected = t.user_id === selectedUid;
      const isEscalated = t.has_escalation == 1;
      return `
        <div class="admin-thread-card ${isSelected ? 'active' : ''}" data-uid="${t.user_id}" style="padding:10px 12px; margin-bottom:8px; border-radius:10px; cursor:pointer; background:${isSelected ? 'rgba(168,85,247,0.18)' : '#140e30'}; border:1px solid ${isSelected ? 'var(--accent-purple)' : (isEscalated ? 'rgba(239,68,68,0.5)' : 'rgba(139,92,246,0.2)')};">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
            <strong style="font-size:0.82rem; color:#fff;">${escapeHtml(t.user_name || 'Player')}</strong>
            ${isEscalated ? '<span style="background:rgba(239,68,68,0.25); color:#ef4444; font-size:0.65rem; font-weight:800; padding:2px 6px; border-radius:4px; border:1px solid #ef4444;">🚨 ESCALATED</span>' : '<span style="font-size:0.68rem; color:var(--accent-emerald);">₹' + Number(t.balance || 0).toFixed(0) + '</span>'}
          </div>
          <div style="font-size:0.72rem; color:#94a3b8; font-family:var(--font-mono); margin-bottom:4px;">${escapeHtml(t.user_email)}</div>
          <div style="font-size:0.74rem; color:#cbd5e1; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
            <span style="color:#a855f7; font-weight:700;">${t.last_sender === 'user' ? 'User: ' : (t.last_sender === 'admin' ? 'Admin: ' : 'AI: ')}</span>${escapeHtml(t.last_message || '')}
          </div>
        </div>
      `;
    }).join('');

    el.supportThreadsList.querySelectorAll('.admin-thread-card').forEach(card => {
      card.addEventListener('click', () => {
        fetchSupportTickets(card.dataset.uid);
      });
    });
  }

  function renderActiveSupportThread(messages, threads, selectedUid) {
    if (!el.supportMessagesContainer) return;
    const currentThread = threads ? threads.find(t => t.user_id === selectedUid) : null;

    if (currentThread) {
      if (el.supportChatUserName) el.supportChatUserName.textContent = currentThread.user_name || 'Player';
      if (el.supportChatUserMeta) el.supportChatUserMeta.textContent = `${currentThread.user_email} • Balance: ₹${Number(currentThread.balance || 0).toFixed(2)}`;
      if (el.supportReplyUserId) el.supportReplyUserId.value = currentThread.user_id;
    }

    if (!messages || messages.length === 0) {
      el.supportMessagesContainer.innerHTML = '<div style="text-align:center; color:#64748b; padding:30px;">No messages in this conversation</div>';
      return;
    }

    el.supportMessagesContainer.innerHTML = messages.map(m => {
      const isUser = m.sender === 'user';
      const isAdmin = m.sender === 'admin';
      const bg = isUser ? 'rgba(147, 51, 234, 0.25)' : (isAdmin ? 'rgba(16, 185, 129, 0.25)' : '#1a1340');
      const border = isUser ? 'rgba(147, 51, 234, 0.5)' : (isAdmin ? 'rgba(16, 185, 129, 0.5)' : 'rgba(139, 92, 246, 0.3)');
      const align = isUser ? 'flex-end' : 'flex-start';
      const senderLabel = isUser ? '👤 User' : (isAdmin ? '👑 You (Admin)' : '🤖 Bombaclat AI');
      const senderColor = isUser ? '#c084fc' : (isAdmin ? '#34d399' : 'var(--accent-cyan)');

      return `
        <div style="align-self:${align}; max-width:82%; margin-bottom:6px;">
          <div style="font-size:0.68rem; font-weight:800; color:${senderColor}; margin-bottom:2px; text-align:${isUser ? 'right' : 'left'};">
            ${senderLabel} <span style="font-weight:400; color:#64748b; font-size:0.64rem;">${m.created_at}</span>
            ${m.is_escalated == 1 ? '<span style="color:#ef4444; margin-left:4px;">🚨 [Escalated]</span>' : ''}
          </div>
          <div style="background:${bg}; border:1px solid ${border}; padding:8px 12px; border-radius:10px; font-size:0.82rem; color:#f8fafc; line-height:1.4;">
            ${escapeHtml(m.message)}
          </div>
        </div>
      `;
    }).join('');

    el.supportMessagesContainer.scrollTop = el.supportMessagesContainer.scrollHeight;
  }

  // Setup Event Listeners
  function setupEvents() {
    // RTP slider change
    if (el.rtpSlider) {
      el.rtpSlider.addEventListener('input', (e) => {
        const val = parseFloat(e.target.value);
        if (el.rtpSliderVal) el.rtpSliderVal.textContent = val.toFixed(1) + '%';
        if (el.houseEdgeDisplay) el.houseEdgeDisplay.textContent = (100 - val).toFixed(1) + '%';
      });
    }

    // RTP presets
    document.querySelectorAll('.btn-preset-pill').forEach(btn => {
      btn.addEventListener('click', () => {
        const rtp = parseFloat(btn.dataset.rtp);
        if (el.rtpSlider) {
          el.rtpSlider.value = rtp;
          if (el.rtpSliderVal) el.rtpSliderVal.textContent = rtp.toFixed(1) + '%';
          if (el.houseEdgeDisplay) el.houseEdgeDisplay.textContent = (100 - rtp).toFixed(1) + '%';
        }
      });
    });

    // Save RTP
    if (el.btnSaveRtp) {
      el.btnSaveRtp.addEventListener('click', async () => {
        const rtp = parseFloat(el.rtpSlider.value);
        try {
          const res = await fetch('api/admin.php?action=update_settings', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ rtp_rate: rtp })
          });
          const data = await res.json();
          if (data.success) {
            alert(`Global RTP successfully updated to ${rtp}%!`);
            fetchStats();
          }
        } catch (e) {
          alert('Failed to save RTP');
        }
      });
    }

    // Save System Settings
    if (el.settingsForm) {
      el.settingsForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(el.settingsForm);
        const payload = {};
        formData.forEach((v, k) => payload[k] = v);
        try {
          const res = await fetch('api/admin.php?action=update_settings', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
          });
          const data = await res.json();
          if (data.success) {
            alert('Parameters saved successfully!');
            fetchSettings();
          }
        } catch (err) {
          alert('Network error');
        }
      });
    }

    // QR Upload
    const qrForm = document.getElementById('qr-upload-form');
    if (qrForm) {
      qrForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(qrForm);
        try {
          const res = await fetch('api/admin.php?action=upload_qr', { method: 'POST', body: formData });
          const data = await res.json();
          if (data.success) {
            alert('QR Code image updated successfully!');
            document.getElementById('qr-preview-img').src = 'assets/images/qrpayment.jpeg?t=' + Date.now();
          } else {
            alert(data.error || 'Upload failed');
          }
        } catch (err) {
          alert('Network error');
        }
      });
    }

    // Admin Password Change Form
    const passwordForm = document.getElementById('admin-password-form');
    if (passwordForm) {
      passwordForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const alertBox = document.getElementById('password-change-alert');
        const submitBtn = document.getElementById('btn-save-password');
        if (alertBox) alertBox.style.display = 'none';

        const curPass = document.getElementById('admin-current-password').value;
        const newPass = document.getElementById('admin-new-password').value;
        const confirmPass = document.getElementById('admin-confirm-password').value;

        if (newPass !== confirmPass) {
          if (alertBox) {
            alertBox.style.display = 'block';
            alertBox.style.background = 'rgba(239, 68, 68, 0.2)';
            alertBox.style.border = '1px solid #ef4444';
            alertBox.style.color = '#fca5a5';
            alertBox.textContent = 'New passwords do not match!';
          }
          return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Updating...';

        try {
          const res = await fetch('api/admin.php?action=change_password', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              current_password: curPass,
              new_password: newPass,
              confirm_password: confirmPass
            })
          });
          const data = await res.json();
          if (alertBox) {
            alertBox.style.display = 'block';
            if (data.success) {
              alertBox.style.background = 'rgba(16, 185, 129, 0.2)';
              alertBox.style.border = '1px solid #10b981';
              alertBox.style.color = '#6ee7b7';
              alertBox.textContent = data.message || 'Admin password updated successfully!';
              passwordForm.reset();
            } else {
              alertBox.style.background = 'rgba(239, 68, 68, 0.2)';
              alertBox.style.border = '1px solid #ef4444';
              alertBox.style.color = '#fca5a5';
              alertBox.textContent = data.error || 'Failed to update password';
            }
          }
        } catch (err) {
          if (alertBox) {
            alertBox.style.display = 'block';
            alertBox.style.background = 'rgba(239, 68, 68, 0.2)';
            alertBox.style.border = '1px solid #ef4444';
            alertBox.style.color = '#fca5a5';
            alertBox.textContent = 'Network error while updating password';
          }
        } finally {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Update Admin Password';
        }
      });
    }

    // Search input
    if (el.userSearchInput) {
      let debounceTimer = null;
      el.userSearchInput.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
          fetchUsers(e.target.value);
        }, 300);
      });
    }

    // Tabs
    el.tabBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        el.tabBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const tab = btn.dataset.tab;
        el.tabPlayers.style.display = tab === 'players' ? 'block' : 'none';
        el.tabDeposits.style.display = tab === 'deposits' ? 'block' : 'none';
        el.tabWithdrawals.style.display = tab === 'withdrawals' ? 'block' : 'none';
        if (el.tabSupport) el.tabSupport.style.display = tab === 'support' ? 'block' : 'none';

        if (tab === 'deposits') fetchDeposits();
        if (tab === 'withdrawals') fetchWithdrawals();
        if (tab === 'support') fetchSupportTickets();
      });
    });

    // Support Tab Refresh
    if (el.btnRefreshSupport) {
      el.btnRefreshSupport.addEventListener('click', () => {
        const activeUid = el.supportReplyUserId ? el.supportReplyUserId.value : null;
        fetchSupportTickets(activeUid);
      });
    }

    // Support Reply Form Submit
    if (el.supportReplyForm) {
      el.supportReplyForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const uid = el.supportReplyUserId.value;
        const msg = el.supportReplyInput.value.trim();
        if (!uid || !msg) return;

        try {
          const res = await fetch('api/admin.php?action=send_support_reply', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: uid, message: msg })
          });
          const data = await res.json();
          if (data.success) {
            el.supportReplyInput.value = '';
            fetchSupportTickets(uid);
          } else {
            alert(data.error || 'Failed to send reply');
          }
        } catch (err) {
          alert('Network error');
        }
      });
    }

    // Modal Close
    document.querySelectorAll('[data-close-modal]').forEach(b => {
      b.addEventListener('click', () => {
        document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('active'));
      });
    });

    // Modal Generate Password
    const genPassBtn = document.getElementById('btn-modal-gen-pass');
    if (genPassBtn) {
      genPassBtn.addEventListener('click', () => {
        const randPass = 'Player' + Math.floor(100000 + Math.random() * 900000) + '!';
        el.modalUserNewPass.value = randPass;
      });
    }

    // Modal Set Password
    const setPassBtn = document.getElementById('btn-modal-set-pass');
    if (setPassBtn) {
      setPassBtn.addEventListener('click', async () => {
        const userId = el.modalUserId.value;
        const pass = el.modalUserNewPass.value;
        if (!pass || pass.length < 6) {
          alert('Password must be at least 6 characters');
          return;
        }
        try {
          const res = await fetch('api/admin.php?action=reset_password', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId, new_password: pass })
          });
          const data = await res.json();
          if (data.success) {
            alert(`Password for user successfully set to:\n\n${pass}\n\nPlease share this key with the player.`);
          }
        } catch (e) {
          alert('Error resetting password');
        }
      });
    }

    // Modal Update Balance
    const updateBalBtn = document.getElementById('btn-modal-update-bal');
    if (updateBalBtn) {
      updateBalBtn.addEventListener('click', async () => {
        const userId = el.modalUserId.value;
        const bal = parseFloat(el.modalUserBalance.value);
        if (isNaN(bal) || bal < 0) {
          alert('Enter a valid non-negative balance');
          return;
        }
        try {
          const res = await fetch('api/admin.php?action=update_balance', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId, balance: bal })
          });
          const data = await res.json();
          if (data.success) {
            alert('Balance updated to ₹' + bal);
            fetchUsers(el.userSearchInput.value);
            fetchStats();
          }
        } catch (e) {
          alert('Error updating balance');
        }
      });
    }

    // Modal Ban / Unban
    const banBtn = document.getElementById('btn-modal-ban');
    const unbanBtn = document.getElementById('btn-modal-unban');
    if (banBtn) {
      banBtn.addEventListener('click', async () => {
        const reason = prompt('Enter reason for ban:', 'Violated fair play policy');
        if (reason !== null) {
          await toggleBan(el.modalUserId.value, 1, reason);
        }
      });
    }
    if (unbanBtn) {
      unbanBtn.addEventListener('click', async () => {
        await toggleBan(el.modalUserId.value, 0, '');
      });
    }

    async function toggleBan(userId, isBanned, reason) {
      try {
        const res = await fetch('api/admin.php?action=toggle_ban', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ user_id: userId, is_banned: isBanned, ban_reason: reason })
        });
        const data = await res.json();
        if (data.success) {
          alert(data.message);
          fetchUsers(el.userSearchInput.value);
        }
      } catch (e) {
        alert('Network error');
      }
    }

    // Modal Save All Profile
    const saveAllBtn = document.getElementById('btn-modal-save-all');
    if (saveAllBtn) {
      saveAllBtn.addEventListener('click', async () => {
        const userId = el.modalUserId.value;
        const rig = el.modalUserRig.value;
        const rtp = el.modalUserRtp.value !== '' ? parseFloat(el.modalUserRtp.value) : null;
        try {
          const res = await fetch('api/admin.php?action=update_user_rig', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId, rig_mode: rig, custom_rtp: rtp })
          });
          const data = await res.json();
          if (data.success) {
            alert('Player profile & rig settings saved!');
            if (el.manageModal) el.manageModal.classList.remove('active');
            fetchUsers(el.userSearchInput.value);
          }
        } catch (e) {
          alert('Network error');
        }
      });
    }

    // Confirm Add Money in Modal
    if (el.btnConfirmAddMoney) {
      el.btnConfirmAddMoney.addEventListener('click', async () => {
        const userId = el.addMoneyUserId.value;
        const amt = parseFloat(el.addMoneyAmountInput.value);
        if (isNaN(amt) || amt <= 0) {
          alert('Please enter a valid positive amount to add');
          return;
        }
        await adjustUserBalance(userId, amt, 'add');
        if (el.addMoneyModal) el.addMoneyModal.classList.remove('active');
      });
    }

    // Confirm Deduct Money in Modal
    if (el.btnConfirmDeductMoney) {
      el.btnConfirmDeductMoney.addEventListener('click', async () => {
        const userId = el.deductMoneyUserId.value;
        const amt = parseFloat(el.deductMoneyAmountInput.value);
        if (isNaN(amt) || amt <= 0) {
          alert('Please enter a valid positive amount to deduct');
          return;
        }
        await adjustUserBalance(userId, amt, 'deduct');
        if (el.deductMoneyModal) el.deductMoneyModal.classList.remove('active');
      });
    }
  }

  function escapeHtml(t) {
    if (!t) return '';
    const d = document.createElement('div');
    d.textContent = t;
    return d.innerHTML;
  }

  // Init
  fetchStats();
  fetchSettings();
  fetchUsers();
  setupEvents();
});

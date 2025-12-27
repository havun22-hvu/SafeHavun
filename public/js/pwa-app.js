/**
 * SafeHavun PWA Application
 */
const PWA = {
    config: {
        isAuthenticated: false,
        csrfToken: '',
        version: '1.0.0'
    },
    state: {
        currentTab: 'dashboard',
        pin: '',
        deviceFingerprint: null,
        deviceInfo: null,
        deferredPrompt: null,
        refreshing: false
    },

    /**
     * Initialize PWA
     */
    init(config) {
        this.config = { ...this.config, ...config };

        // Setup navigation
        this.setupNavigation();

        // Setup service worker
        this.setupServiceWorker();

        // Setup install prompt
        this.setupInstallPrompt();

        // Generate fingerprint
        this.generateFingerprint();

        // Load initial data
        this.loadDashboardData();

        // Setup pull to refresh
        this.setupPullToRefresh();

        // Update auth state
        this.updateAuthState();

        // Auto refresh every 5 minutes
        setInterval(() => this.refreshData(), 300000);
    },

    /**
     * Setup tab navigation
     */
    setupNavigation() {
        document.querySelectorAll('.nav-item').forEach(btn => {
            btn.addEventListener('click', () => {
                const tab = btn.dataset.tab;
                this.switchTab(tab);
            });
        });

        // Signal filters
        document.querySelectorAll('.signal-filter').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.signal-filter').forEach(b => {
                    b.classList.remove('active', 'bg-emerald-500/20', 'text-emerald-400');
                    b.classList.add('bg-white/5', 'text-gray-400');
                });
                btn.classList.add('active', 'bg-emerald-500/20', 'text-emerald-400');
                btn.classList.remove('bg-white/5', 'text-gray-400');
                this.filterSignals(btn.dataset.filter);
            });
        });
    },

    /**
     * Switch active tab
     */
    switchTab(tab) {
        this.state.currentTab = tab;

        // Update tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        document.getElementById(`tab-${tab}`).classList.add('active');

        // Update nav items
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.remove('active');
            if (item.dataset.tab === tab) {
                item.classList.add('active');
            }
        });

        // Load tab-specific data
        switch(tab) {
            case 'dashboard':
                this.loadDashboardData();
                break;
            case 'portfolio':
                this.loadPortfolioData();
                break;
            case 'signals':
                this.loadSignalsData();
                break;
            case 'settings':
                this.updateSettingsTab();
                break;
        }
    },

    /**
     * Setup Service Worker
     */
    setupServiceWorker() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').then(registration => {
                // Check for updates every 5 minutes
                setInterval(() => registration.update(), 300000);

                // Listen for new service worker
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            this.showUpdateBanner();
                        }
                    });
                });
            });

            // Handle controller change
            navigator.serviceWorker.addEventListener('controllerchange', () => {
                if (!this.state.refreshing) {
                    this.state.refreshing = true;
                    this.showToast('App geupdate!', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
        }
    },

    /**
     * Setup install prompt
     */
    setupInstallPrompt() {
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.state.deferredPrompt = e;
            document.getElementById('install-app-btn')?.classList.remove('hidden');
        });
    },

    /**
     * Install app
     */
    installApp() {
        if (!this.state.deferredPrompt) return;

        this.state.deferredPrompt.prompt();
        this.state.deferredPrompt.userChoice.then((result) => {
            if (result.outcome === 'accepted') {
                document.getElementById('install-app-btn')?.classList.add('hidden');
                this.showToast('App geinstalleerd!', 'success');
            }
            this.state.deferredPrompt = null;
        });
    },

    /**
     * Show update banner
     */
    showUpdateBanner() {
        document.getElementById('updateBanner').classList.remove('hidden');
    },

    /**
     * Update app
     */
    checkForUpdates() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistration().then(registration => {
                if (registration) {
                    registration.update().then(() => {
                        this.showToast('Update check voltooid', 'info');
                    });
                }
            });
        }
    },

    /**
     * Clear cache
     */
    clearCache() {
        if ('caches' in window) {
            caches.keys().then(names => {
                names.forEach(name => caches.delete(name));
            }).then(() => {
                this.showToast('Cache gewist', 'success');
                setTimeout(() => window.location.reload(), 1000);
            });
        }
    },

    /**
     * Setup pull to refresh
     */
    setupPullToRefresh() {
        let startY = 0;
        let pulling = false;

        document.addEventListener('touchstart', (e) => {
            if (window.scrollY === 0) {
                startY = e.touches[0].pageY;
                pulling = true;
            }
        }, { passive: true });

        document.addEventListener('touchmove', (e) => {
            if (!pulling) return;
            const y = e.touches[0].pageY;
            const diff = y - startY;
            if (diff > 80) {
                pulling = false;
                this.refreshData();
            }
        }, { passive: true });

        document.addEventListener('touchend', () => {
            pulling = false;
        }, { passive: true });
    },

    /**
     * Refresh current tab data
     */
    refreshData() {
        const btn = document.getElementById('refreshBtn');
        btn?.classList.add('animate-spin');

        switch(this.state.currentTab) {
            case 'dashboard':
                this.loadDashboardData();
                break;
            case 'portfolio':
                this.loadPortfolioData();
                break;
            case 'signals':
                this.loadSignalsData();
                break;
        }

        setTimeout(() => btn?.classList.remove('animate-spin'), 1000);
    },

    /**
     * Generate device fingerprint
     */
    async generateFingerprint() {
        const components = [
            navigator.userAgent,
            navigator.language,
            screen.width + 'x' + screen.height,
            screen.colorDepth,
            new Date().getTimezoneOffset(),
            navigator.hardwareConcurrency || '',
            navigator.platform
        ];

        const data = components.join('|');
        const encoder = new TextEncoder();
        const dataBuffer = encoder.encode(data);
        const hashBuffer = await crypto.subtle.digest('SHA-256', dataBuffer);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        this.state.deviceFingerprint = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    },

    /**
     * CSRF-safe fetch
     */
    async csrfFetch(url, options = {}) {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.config.csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            ...options.headers
        };

        try {
            const response = await fetch(url, { ...options, headers, credentials: 'same-origin' });

            if (response.status === 419) {
                // CSRF token expired, reload
                window.location.reload();
                return null;
            }

            return response;
        } catch (error) {
            console.error('Fetch error:', error);
            return null;
        }
    },

    /**
     * Load dashboard data
     */
    async loadDashboardData() {
        try {
            // Load market overview
            const overviewRes = await fetch('/api/market-overview');
            if (overviewRes.ok) {
                const data = await overviewRes.json();
                this.updateSentiment(data);
            }

            // Load prices
            const pricesRes = await fetch('/api/prices');
            if (pricesRes.ok) {
                const prices = await pricesRes.json();
                this.updateAssets(prices);
            }

            // Load whale alerts
            const whaleRes = await fetch('/api/whale-alerts');
            if (whaleRes.ok) {
                const whales = await whaleRes.json();
                this.updateWhalePreview(whales);
            }
        } catch (error) {
            console.error('Dashboard load error:', error);
        }
    },

    /**
     * Update sentiment display
     */
    updateSentiment(data) {
        const sentimentText = document.getElementById('sentiment-text');
        const sentimentStrength = document.getElementById('sentiment-strength');
        const sentimentArrow = document.getElementById('sentiment-arrow');
        const sentimentIndicator = document.getElementById('sentiment-indicator');
        const sentimentAdvice = document.getElementById('sentiment-advice');

        if (sentimentText) sentimentText.textContent = data.overall_sentiment === 'bullish' ? 'Koopzone' :
                                                        data.overall_sentiment === 'bearish' ? 'Verkoopzone' : 'Neutraal';
        if (sentimentStrength) sentimentStrength.textContent = `Sterkte: ${data.overall_strength}%`;
        if (sentimentAdvice) sentimentAdvice.textContent = data.advice;

        if (sentimentArrow) {
            sentimentArrow.textContent = data.overall_sentiment === 'bullish' ? '↑' :
                                         data.overall_sentiment === 'bearish' ? '↓' : '→';
        }

        if (sentimentIndicator) {
            sentimentIndicator.className = 'w-20 h-20 rounded-full flex items-center justify-center text-4xl ' +
                (data.overall_sentiment === 'bullish' ? 'bg-bullish/20 text-bullish' :
                 data.overall_sentiment === 'bearish' ? 'bg-bearish/20 text-bearish' : 'bg-gray-500/20 text-gray-400');
        }

        // Update signal counts
        document.getElementById('bullish-count').textContent = data.bullish_signals || 0;
        document.getElementById('bearish-count').textContent = data.bearish_signals || 0;

        // Update Fear & Greed
        if (data.fear_greed) {
            const fg = data.fear_greed;
            document.getElementById('fg-value').textContent = fg.value;
            document.getElementById('fg-classification').textContent = fg.classification;

            const fgCircle = document.getElementById('fg-circle');
            let bgClass = 'bg-gray-500/20 text-gray-400';
            if (fg.value <= 25) bgClass = 'bg-red-500/20 text-red-400';
            else if (fg.value <= 45) bgClass = 'bg-orange-500/20 text-orange-400';
            else if (fg.value <= 55) bgClass = 'bg-yellow-500/20 text-yellow-400';
            else if (fg.value <= 75) bgClass = 'bg-lime-500/20 text-lime-400';
            else bgClass = 'bg-green-500/20 text-green-400';

            fgCircle.className = `w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold ${bgClass}`;

            const hint = document.getElementById('fg-hint');
            if (fg.value <= 25) hint.textContent = 'Potentiele bodem';
            else if (fg.value >= 75) hint.textContent = 'Potentiele top';
            else hint.textContent = '';
        }
    },

    /**
     * Update assets display
     */
    updateAssets(prices) {
        const topAssets = document.getElementById('top-assets');
        const allAssets = document.getElementById('all-assets');

        const cryptos = prices.filter(p => p.type === 'crypto');
        const top5 = cryptos.slice(0, 5);

        // Top assets
        topAssets.innerHTML = top5.map(asset => `
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <span class="font-semibold">${asset.symbol}</span>
                </div>
                <div class="text-right">
                    <span class="font-mono">${this.formatPrice(asset.price_eur)}</span>
                    ${asset.price_change_24h ? `
                        <span class="text-sm ml-2 ${asset.price_change_24h >= 0 ? 'text-bullish' : 'text-bearish'}">
                            ${asset.price_change_24h >= 0 ? '+' : ''}${asset.price_change_24h.toFixed(1)}%
                        </span>
                    ` : ''}
                </div>
            </div>
        `).join('');

        // All assets
        allAssets.innerHTML = cryptos.map(asset => `
            <div class="p-4 hover:bg-white/5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold">${asset.symbol}</p>
                        <p class="text-sm text-gray-400">${asset.name}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-mono">${this.formatPrice(asset.price_eur)}</p>
                        <div class="flex space-x-2 text-sm">
                            ${asset.price_change_24h ? `
                                <span class="${asset.price_change_24h >= 0 ? 'text-bullish' : 'text-bearish'}">
                                    ${asset.price_change_24h >= 0 ? '+' : ''}${asset.price_change_24h.toFixed(1)}%
                                </span>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    },

    /**
     * Update whale preview
     */
    updateWhalePreview(whales) {
        const preview = document.getElementById('whale-preview');
        const recent = whales.slice(0, 2);

        if (recent.length === 0) {
            preview.innerHTML = '<p class="text-gray-500 text-center py-2">Geen recente whale activiteit</p>';
            return;
        }

        preview.innerHTML = recent.map(w => `
            <div class="flex items-center space-x-3 p-2 bg-white/5 rounded-lg">
                <span class="text-xl ${w.direction === 'exchange_outflow' ? 'text-bullish' : 'text-bearish'}">
                    ${w.direction === 'exchange_outflow' ? '↑' : '↓'}
                </span>
                <div class="flex-1">
                    <p class="text-sm font-medium">${w.amount.toFixed(2)} ${w.asset_symbol}</p>
                    <p class="text-xs text-gray-400">${w.time_ago}</p>
                </div>
            </div>
        `).join('');
    },

    /**
     * Load portfolio data
     */
    async loadPortfolioData() {
        if (!this.config.isAuthenticated) {
            document.getElementById('portfolio-auth-required').classList.remove('hidden');
            document.getElementById('portfolio-setup-required').classList.add('hidden');
            document.getElementById('portfolio-content').classList.add('hidden');
            return;
        }

        document.getElementById('portfolio-auth-required').classList.add('hidden');

        try {
            const res = await this.csrfFetch('/api/portfolio');
            if (!res) return;

            if (res.status === 404) {
                // No Bitvavo connected
                document.getElementById('portfolio-setup-required').classList.remove('hidden');
                document.getElementById('portfolio-content').classList.add('hidden');
                return;
            }

            if (res.ok) {
                const data = await res.json();
                document.getElementById('portfolio-setup-required').classList.add('hidden');
                document.getElementById('portfolio-content').classList.remove('hidden');
                this.updatePortfolio(data);
            }
        } catch (error) {
            console.error('Portfolio load error:', error);
        }
    },

    /**
     * Update portfolio display
     */
    updatePortfolio(data) {
        document.getElementById('portfolio-total').textContent = this.formatEuro(data.total_value);
        document.getElementById('portfolio-cost').textContent = this.formatEuro(data.total_cost);

        const pnl = document.getElementById('portfolio-pnl');
        const pnlPercent = document.getElementById('portfolio-return');
        const isProfit = data.total_profit_loss >= 0;

        pnl.textContent = (isProfit ? '+' : '') + this.formatEuro(data.total_profit_loss);
        pnl.className = `text-xl font-semibold ${isProfit ? 'text-emerald-400' : 'text-red-400'}`;

        pnlPercent.textContent = (isProfit ? '+' : '') + data.total_profit_loss_percent.toFixed(1) + '%';
        pnlPercent.className = `font-semibold ${isProfit ? 'text-emerald-400' : 'text-red-400'}`;

        // Holdings
        const holdingsEl = document.getElementById('portfolio-holdings');
        if (data.holdings.length === 0) {
            holdingsEl.innerHTML = '<p class="p-4 text-center text-gray-400">Geen holdings gevonden</p>';
        } else {
            holdingsEl.innerHTML = data.holdings.map(h => `
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-blue-500 rounded-full flex items-center justify-center font-bold text-sm">
                                ${h.asset.substring(0, 2)}
                            </div>
                            <div>
                                <p class="font-semibold">${h.asset}</p>
                                <p class="text-sm text-gray-400">${h.total_amount.toFixed(6)}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold">${this.formatEuro(h.current_value)}</p>
                            <p class="text-sm ${h.profit_loss >= 0 ? 'text-emerald-400' : 'text-red-400'}">
                                ${h.profit_loss >= 0 ? '+' : ''}${h.profit_loss_percent.toFixed(1)}%
                            </p>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Last sync
        if (data.last_sync) {
            document.getElementById('last-sync').textContent = `Laatste sync: ${data.last_sync}`;
        }
    },

    /**
     * Connect Bitvavo
     */
    async connectBitvavo() {
        const apiKey = document.getElementById('bitvavo-api-key').value.trim();
        const apiSecret = document.getElementById('bitvavo-api-secret').value.trim();
        const errorEl = document.getElementById('bitvavo-setup-error');
        const btn = document.getElementById('bitvavo-connect-btn');

        // Validate
        if (!apiKey || !apiSecret) {
            errorEl.textContent = 'Vul beide velden in';
            errorEl.classList.remove('hidden');
            return;
        }

        // Show loading
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-5 h-5 animate-spin" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><span>Verbinden...</span>';
        errorEl.classList.add('hidden');

        try {
            const res = await this.csrfFetch('/api/portfolio/connect', {
                method: 'POST',
                body: JSON.stringify({
                    api_key: apiKey,
                    api_secret: apiSecret
                })
            });

            if (!res) {
                throw new Error('Geen verbinding');
            }

            const data = await res.json();

            if (res.ok && data.success) {
                this.showToast('Bitvavo gekoppeld!', 'success');
                // Clear form
                document.getElementById('bitvavo-api-key').value = '';
                document.getElementById('bitvavo-api-secret').value = '';
                // Reload portfolio
                this.loadPortfolioData();
            } else {
                errorEl.textContent = data.error || 'Koppeling mislukt';
                errorEl.classList.remove('hidden');
            }
        } catch (error) {
            errorEl.textContent = 'Verbinding mislukt: ' + error.message;
            errorEl.classList.remove('hidden');
        }

        btn.disabled = false;
        btn.innerHTML = '<span>Koppelen</span>';
    },

    /**
     * Disconnect Bitvavo
     */
    async disconnectBitvavo() {
        if (!confirm('Weet je zeker dat je Bitvavo wilt ontkoppelen?')) return;

        try {
            const res = await this.csrfFetch('/api/portfolio/disconnect', {
                method: 'DELETE'
            });

            if (res && res.ok) {
                this.showToast('Bitvavo ontkoppeld', 'info');
                this.loadPortfolioData();
            }
        } catch (error) {
            this.showToast('Ontkoppelen mislukt', 'error');
        }
    },

    /**
     * Sync portfolio
     */
    async syncPortfolio() {
        const btn = document.getElementById('sync-btn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';

        try {
            const res = await this.csrfFetch('/api/portfolio/sync', { method: 'POST' });
            if (res && res.ok) {
                this.showToast('Portfolio gesynchroniseerd', 'success');
                this.loadPortfolioData();
            } else {
                this.showToast('Sync mislukt', 'error');
            }
        } catch (error) {
            this.showToast('Sync mislukt', 'error');
        }

        btn.disabled = false;
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg><span>Sync</span>';
    },

    /**
     * Load signals data
     */
    async loadSignalsData() {
        try {
            // Load signals
            const signalsRes = await fetch('/api/signals');
            if (signalsRes.ok) {
                const signals = await signalsRes.json();
                this.updateSignals(signals);
            }

            // Load whale alerts
            const whaleRes = await fetch('/api/whale-alerts');
            if (whaleRes.ok) {
                const whales = await whaleRes.json();
                this.updateWhaleAlerts(whales);
            }
        } catch (error) {
            console.error('Signals load error:', error);
        }
    },

    /**
     * Update signals display
     */
    updateSignals(signals) {
        document.getElementById('signals-count').textContent = `${signals.length} signalen`;

        const list = document.getElementById('signals-list');
        if (signals.length === 0) {
            list.innerHTML = '<p class="p-4 text-center text-gray-400">Geen actieve signalen</p>';
            return;
        }

        list.innerHTML = signals.map(s => `
            <div class="signal-item p-4" data-type="${s.signal_type}">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl ${s.signal_type === 'bullish' ? 'text-bullish' : 'text-bearish'}">
                        ${s.signal_type === 'bullish' ? '↑' : '↓'}
                    </span>
                    <div class="flex-1">
                        <p class="font-medium">${s.description}</p>
                        <p class="text-sm text-gray-400">${s.indicator} - ${s.time_ago}</p>
                    </div>
                    <span class="px-2 py-1 rounded text-xs ${s.signal_type === 'bullish' ? 'bg-bullish/20 text-bullish' : 'bg-bearish/20 text-bearish'}">
                        ${s.strength}%
                    </span>
                </div>
            </div>
        `).join('');
    },

    /**
     * Update whale alerts display
     */
    updateWhaleAlerts(whales) {
        // Update summary
        const outflows = whales.filter(w => w.direction === 'exchange_outflow').length;
        const inflows = whales.filter(w => w.direction === 'exchange_inflow').length;

        document.getElementById('whale-outflow').textContent = outflows;
        document.getElementById('whale-inflow').textContent = inflows;

        const list = document.getElementById('whale-alerts');
        if (whales.length === 0) {
            list.innerHTML = '<p class="p-4 text-center text-gray-400">Geen whale transacties</p>';
            return;
        }

        list.innerHTML = whales.map(w => `
            <div class="whale-item p-4" data-direction="${w.direction}">
                <div class="flex items-center space-x-3">
                    <span class="text-xl ${w.direction === 'exchange_outflow' ? 'text-bullish' : w.direction === 'exchange_inflow' ? 'text-bearish' : 'text-gray-400'}">
                        ${w.direction === 'exchange_outflow' ? '↑' : w.direction === 'exchange_inflow' ? '↓' : '↔'}
                    </span>
                    <div class="flex-1">
                        <p class="font-medium">${w.amount.toFixed(2)} ${w.asset_symbol}</p>
                        <p class="text-sm text-gray-400">
                            ${w.amount_usd ? `~$${(w.amount_usd / 1000000).toFixed(1)}M - ` : ''}${w.time_ago}
                        </p>
                    </div>
                    <span class="px-2 py-1 rounded text-xs ${w.direction === 'exchange_outflow' ? 'bg-bullish/20 text-bullish' : w.direction === 'exchange_inflow' ? 'bg-bearish/20 text-bearish' : 'bg-gray-500/20 text-gray-400'}">
                        ${w.direction === 'exchange_outflow' ? 'BULLISH' : w.direction === 'exchange_inflow' ? 'BEARISH' : 'NEUTRAL'}
                    </span>
                </div>
            </div>
        `).join('');
    },

    /**
     * Filter signals
     */
    filterSignals(filter) {
        const items = document.querySelectorAll('.signal-item');
        const whaleItems = document.querySelectorAll('.whale-item');

        items.forEach(item => {
            if (filter === 'all' || filter === 'whale') {
                item.style.display = '';
            } else {
                item.style.display = item.dataset.type === filter ? '' : 'none';
            }
        });

        // Show/hide whale section
        const whaleSection = document.getElementById('whale-alerts').closest('.glass');
        if (whaleSection) {
            whaleSection.style.display = (filter === 'all' || filter === 'whale') ? '' : 'none';
        }
    },

    /**
     * Update settings tab
     */
    async updateSettingsTab() {
        if (this.config.isAuthenticated) {
            document.getElementById('login-prompt').classList.add('hidden');
            document.getElementById('user-info').classList.remove('hidden');
            document.getElementById('logout-btn').classList.remove('hidden');

            // Check Bitvavo status
            try {
                const res = await this.csrfFetch('/api/portfolio');
                if (res && res.status === 200) {
                    // Bitvavo is connected
                    document.getElementById('bitvavo-status-text').textContent = 'Gekoppeld';
                    document.getElementById('bitvavo-status-text').classList.remove('text-gray-400');
                    document.getElementById('bitvavo-status-text').classList.add('text-emerald-400');
                    document.getElementById('bitvavo-connect-action').classList.add('hidden');
                    document.getElementById('bitvavo-disconnect-action').classList.remove('hidden');
                } else {
                    // Not connected
                    document.getElementById('bitvavo-status-text').textContent = 'Niet gekoppeld';
                    document.getElementById('bitvavo-status-text').classList.add('text-gray-400');
                    document.getElementById('bitvavo-status-text').classList.remove('text-emerald-400');
                    document.getElementById('bitvavo-connect-action').classList.remove('hidden');
                    document.getElementById('bitvavo-disconnect-action').classList.add('hidden');
                }
            } catch (e) {
                // Error checking
            }
        } else {
            document.getElementById('login-prompt').classList.remove('hidden');
            document.getElementById('user-info').classList.add('hidden');
            document.getElementById('logout-btn').classList.add('hidden');
            document.getElementById('bitvavo-connect-action').classList.remove('hidden');
            document.getElementById('bitvavo-disconnect-action').classList.add('hidden');
        }
    },

    /**
     * Update auth state across app
     */
    updateAuthState() {
        this.updateSettingsTab();
    },

    /**
     * Show auth modal
     */
    async showAuth() {
        const modal = document.getElementById('authModal');
        modal.classList.remove('hidden');

        // Show loading
        document.getElementById('auth-loading').classList.remove('hidden');
        document.getElementById('auth-pin').classList.add('hidden');
        document.getElementById('auth-register').classList.add('hidden');

        // Check device
        try {
            const res = await this.csrfFetch('/auth/pin/check-device', {
                method: 'POST',
                body: JSON.stringify({ device_fingerprint: this.state.deviceFingerprint })
            });

            if (!res) return;
            const data = await res.json();

            document.getElementById('auth-loading').classList.add('hidden');

            if (data.has_device) {
                document.getElementById('auth-pin').classList.remove('hidden');
                this.state.deviceInfo = data;

                // Show biometric button if available
                if (data.has_biometric) {
                    document.getElementById('biometric-btn').style.display = '';
                }

                // Show passkey option if available
                if (data.has_passkey) {
                    document.getElementById('passkey-option').classList.remove('hidden');
                }
            } else {
                document.getElementById('auth-register').classList.remove('hidden');
            }
        } catch (error) {
            console.error('Device check error:', error);
            document.getElementById('auth-loading').classList.add('hidden');
            document.getElementById('auth-register').classList.remove('hidden');
        }
    },

    /**
     * Hide auth modal
     */
    hideAuth() {
        document.getElementById('authModal').classList.add('hidden');
        this.state.pin = '';
        this.updatePinDots();
    },

    /**
     * Enter PIN digit
     */
    enterPin(digit) {
        if (this.state.pin.length >= 4) return;

        this.state.pin += digit;
        this.updatePinDots();

        if (this.state.pin.length === 4) {
            this.submitPin();
        }
    },

    /**
     * Delete PIN digit
     */
    deletePin() {
        this.state.pin = this.state.pin.slice(0, -1);
        this.updatePinDots();
    },

    /**
     * Update PIN dots display
     */
    updatePinDots() {
        const dots = document.querySelectorAll('.pin-dot');
        dots.forEach((dot, i) => {
            if (i < this.state.pin.length) {
                dot.classList.add('filled');
            } else {
                dot.classList.remove('filled');
            }
        });
    },

    /**
     * Submit PIN
     */
    async submitPin() {
        const errorEl = document.getElementById('pin-error');
        errorEl.classList.add('hidden');

        try {
            const res = await this.csrfFetch('/auth/pin/login', {
                method: 'POST',
                body: JSON.stringify({
                    device_fingerprint: this.state.deviceFingerprint,
                    pin: this.state.pin
                })
            });

            if (!res) return;

            if (res.ok) {
                this.config.isAuthenticated = true;
                this.hideAuth();
                this.updateAuthState();
                this.showToast('Ingelogd!', 'success');

                // Reload current tab
                this.switchTab(this.state.currentTab);
            } else {
                const data = await res.json();
                errorEl.textContent = data.message || 'Ongeldige PIN';
                errorEl.classList.remove('hidden');
                this.state.pin = '';
                this.updatePinDots();

                // Shake animation
                document.querySelectorAll('.pin-dot').forEach(dot => {
                    dot.style.animation = 'shake 0.5s';
                    setTimeout(() => dot.style.animation = '', 500);
                });
            }
        } catch (error) {
            console.error('PIN submit error:', error);
            this.state.pin = '';
            this.updatePinDots();
        }
    },

    /**
     * Use biometric
     */
    async useBiometric() {
        // Trigger passkey/biometric auth
        this.usePasskey();
    },

    /**
     * Use passkey
     */
    async usePasskey() {
        try {
            // Get options
            const optionsRes = await this.csrfFetch('/auth/passkey/login/options', {
                method: 'POST',
                body: JSON.stringify({})
            });

            if (!optionsRes || !optionsRes.ok) {
                this.showToast('Passkey niet beschikbaar', 'error');
                return;
            }

            const options = await optionsRes.json();

            // Convert base64url to buffer
            options.challenge = this.base64urlToBuffer(options.challenge);
            if (options.allowCredentials) {
                options.allowCredentials = options.allowCredentials.map(c => ({
                    ...c,
                    id: this.base64urlToBuffer(c.id)
                }));
            }

            // Get credential
            const credential = await navigator.credentials.get({ publicKey: options });

            // Send to server
            const authRes = await this.csrfFetch('/auth/passkey/login', {
                method: 'POST',
                body: JSON.stringify({
                    id: credential.id,
                    rawId: this.bufferToBase64url(credential.rawId),
                    response: {
                        authenticatorData: this.bufferToBase64url(credential.response.authenticatorData),
                        clientDataJSON: this.bufferToBase64url(credential.response.clientDataJSON),
                        signature: this.bufferToBase64url(credential.response.signature),
                        userHandle: credential.response.userHandle ? this.bufferToBase64url(credential.response.userHandle) : null
                    },
                    type: credential.type
                })
            });

            if (authRes && authRes.ok) {
                this.config.isAuthenticated = true;
                this.hideAuth();
                this.updateAuthState();
                this.showToast('Ingelogd!', 'success');
                this.switchTab(this.state.currentTab);
            } else {
                this.showToast('Passkey verificatie mislukt', 'error');
            }
        } catch (error) {
            console.error('Passkey error:', error);
            this.showToast('Passkey mislukt', 'error');
        }
    },

    /**
     * Logout
     */
    async logout() {
        try {
            await this.csrfFetch('/logout', { method: 'POST' });
            this.config.isAuthenticated = false;
            this.updateAuthState();
            this.showToast('Uitgelogd', 'info');
            this.switchTab('dashboard');
        } catch (error) {
            console.error('Logout error:', error);
        }
    },

    /**
     * Show toast message
     */
    showToast(message, type = 'info') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');

        const colors = {
            success: 'bg-emerald-500',
            error: 'bg-red-500',
            info: 'bg-blue-500',
            warning: 'bg-orange-500'
        };

        toast.className = `toast ${colors[type]} text-white px-4 py-3 rounded-xl shadow-lg`;
        toast.textContent = message;

        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    },

    /**
     * Format price
     */
    formatPrice(price) {
        if (!price) return '-';
        return '€' + price.toLocaleString('nl-NL', {
            minimumFractionDigits: price < 1 ? 4 : 2,
            maximumFractionDigits: price < 1 ? 4 : 2
        });
    },

    /**
     * Format euro
     */
    formatEuro(amount) {
        if (amount === null || amount === undefined) return '-';
        return '€' + amount.toLocaleString('nl-NL', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    },

    /**
     * Base64url to buffer
     */
    base64urlToBuffer(base64url) {
        const padding = '='.repeat((4 - base64url.length % 4) % 4);
        const base64 = base64url.replace(/-/g, '+').replace(/_/g, '/') + padding;
        const rawData = atob(base64);
        return Uint8Array.from(rawData, c => c.charCodeAt(0)).buffer;
    },

    /**
     * Buffer to base64url
     */
    bufferToBase64url(buffer) {
        const bytes = new Uint8Array(buffer);
        let binary = '';
        bytes.forEach(b => binary += String.fromCharCode(b));
        return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
    }
};

// Update app function (global for HTML onclick)
function updateApp() {
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistration().then(registration => {
            if (registration && registration.waiting) {
                registration.waiting.postMessage({ type: 'SKIP_WAITING' });
            }
        });
    }
    window.location.reload();
}

// Refresh data function (global)
function refreshData() {
    PWA.refreshData();
}

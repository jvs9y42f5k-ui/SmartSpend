// SmartSpend Simple Household Budget Tracker - Core Controller
// Global App State
let state = {
    // Base static categories (Goals will create dynamic savings categories)
    baseCategories: [
        { id: 'house', name: 'House', type: 'expense', icon: 'home', color: '#8b5cf6' },
        { id: 'credit', name: 'Credit card', type: 'expense', icon: 'credit_card', color: '#06b6d4' },
        { id: 'transportation', name: 'Transportation', type: 'expense', icon: 'directions_car', color: '#f59e0b' },
        { id: 'groceries', name: 'Groceries', type: 'expense', icon: 'shopping_cart', color: '#22C55E' },
        { id: 'shopping', name: 'Shopping', type: 'expense', icon: 'shopping_bag', color: '#f43f5e' },
        { id: 'savings_gen', name: 'General Savings', type: 'savings', icon: 'savings', color: '#3b82f6' },
        { id: 'salary', name: 'Salary', type: 'income', icon: 'payments', color: '#22C55E' },
        { id: 'bonus', name: 'Bonus', type: 'income', icon: 'redeem', color: '#8b5cf6' }
    ],
    transactions: [],
    budgets: {},
    goals: [],
    // Pre-populated monthly trends for Analytics Screen
    analyticsData: {
        months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        expenses: {
            'House': [1640, 1640, 1640, 1640, 1640, 1640],
            'Credit card': [720, 810, 680, 750, 790, 840],
            'Transportation': [480, 500, 450, 520, 510, 520],
            'Groceries': [380, 390, 370, 410, 385, 400],
            'Shopping': [250, 180, 300, 150, 220, 120]
        },
        income: {
            'Salary': [4500, 4500, 4500, 5000, 5000, 5000],
            'Bonus': [0, 800, 0, 1200, 0, 1500]
        }
    }
};
// Initial Setup Data (loaded if localStorage is clean)
const initialTransactions = [
    { id: 't1', title: 'Monthly Salary Payment', date: '2026-06-25', amount: 5000.00, type: 'income', inout: 'In', categoryId: 'salary' },
    { id: 't2', title: 'Summer Bonus', date: '2026-06-28', amount: 1500.00, type: 'income', inout: 'In', categoryId: 'bonus' },
    { id: 't3', title: 'Monthly Apartment Rent', date: '2026-06-01', amount: 1640.00, type: 'expense', inout: 'Out', categoryId: 'house' },
    { id: 't4', title: 'Credit Card Bill Payment', date: '2026-06-05', amount: 840.00, type: 'expense', inout: 'Out', categoryId: 'credit' },
    { id: 't5', title: 'Commuter Train Ticket', date: '2026-06-03', amount: 520.00, type: 'expense', inout: 'Out', categoryId: 'transportation' },
    { id: 't6', title: 'Weekly Grocery Store', date: '2026-06-10', amount: 400.00, type: 'expense', inout: 'Out', categoryId: 'groceries' },
    { id: 't7', title: 'Shopping Clothes', date: '2026-06-18', amount: 120.00, type: 'expense', inout: 'Out', categoryId: 'shopping' },
    { id: 't8', title: 'MacBook Savings Deposit', date: '2026-06-27', amount: 200.00, type: 'expense', inout: 'Out', categoryId: 'goal_g1' }
];
const initialBudgets = {
    'house': 1700,
    'credit': 800,         // spent 840 (105% -> Over budget alert)
    'transportation': 600,  // spent 520 (86%)
    'groceries': 350,       // spent 400 (114% -> Over budget alert)
    'shopping': 150,        // spent 120 (80%)
    'savings_gen': 500
};
const initialGoals = [
    { id: 'g1', title: 'New MacBook Pro', targetAmount: 2000.00, startingAmount: 1000.00 },
    { id: 'g2', title: 'Summer Vacation', targetAmount: 1500.00, startingAmount: 450.00 },
    { id: 'g3', title: 'Emergency Fund', targetAmount: 5000.00, startingAmount: 2500.00 }
];
// Active Chart Instances
let charts = {
    donut: null,
    expensesBar: null,
    incomeBar: null,
    analyticsDonut: null
};
// ----------------------------------------------------
// DYNAMIC CATEGORY RESOLVER (Helper for Goals Linking)
// ----------------------------------------------------
function getCategories() {
    const list = [...state.baseCategories];
    
    // Dynamically append goals as categories
    state.goals.forEach(goal => {
        list.push({
            id: `goal_${goal.id}`,
            name: `Goal: ${goal.title}`,
            type: 'savings',
            icon: 'track_changes',
            color: '#3B82F6' // Accent Blue
        });
    });
    return list;
}
// ----------------------------------------------------
// DYNAMIC GOALS CALCULATOR
// ----------------------------------------------------
function getGoalProgress(goal) {
    let progress = Number(goal.startingAmount) || 0;
    
    // Sum transactions belonging to this goal's dynamic category
    state.transactions.forEach(t => {
        if (t.categoryId === `goal_${goal.id}`) {
            progress += Number(t.amount);
        }
    });
    return progress;
}
// ----------------------------------------------------
// STATE LIFECYCLE
// ----------------------------------------------------
function loadState() {
    const saved = localStorage.getItem('smartspend_state');
    if (saved) {
        try {
            state = JSON.parse(saved);
        } catch (e) {
            console.error("Failed to parse state, resetting.", e);
            initializeDefaultState();
        }
    } else {
        initializeDefaultState();
    }
}
function initializeDefaultState() {
    state.transactions = [...initialTransactions];
    state.budgets = { ...initialBudgets };
    state.goals = [...initialGoals];
    saveState();
}
function saveState() {
    localStorage.setItem('smartspend_state', JSON.stringify(state));
}
// ----------------------------------------------------
// ROUTING
// ----------------------------------------------------
function handleRouting() {
    const hash = window.location.hash || '#home';
    const viewName = hash.substring(1);
    
    document.querySelectorAll('.view-section').forEach(sec => sec.classList.remove('active-view'));
    document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
    
    const targetSection = document.getElementById(`view-${viewName}`);
    const targetNavItem = document.getElementById(`nav-${viewName}`);
    
    if (targetSection && targetNavItem) {
        targetSection.classList.add('active-view');
        targetNavItem.classList.add('active');
        
        const titleText = targetNavItem.querySelector('.nav-label').textContent;
        document.getElementById('page-title').textContent = titleText;
    }
    
    renderActiveView(viewName);
}
function renderActiveView(viewName) {
    switch (viewName) {
        case 'home':
            updateMetrics();
            renderRecentTransactions();
            renderHomeDonutChart();
            break;
        case 'analytics':
            renderAnalyticsCharts();
            break;
        case 'budget':
            renderBudgets();
            break;
        case 'goals':
            renderGoals();
            break;
        case 'transactions':
            populateFormCategoryDropdown();
            renderTransactionsList();
            break;
    }
}
// ----------------------------------------------------
// HOME VIEW: METRICS & RECENT TRANSACTIONS
// ----------------------------------------------------
function getCategorySpentMap() {
    const spentMap = {};
    getCategories().forEach(cat => {
        spentMap[cat.id] = 0;
    });
    
    state.transactions.forEach(t => {
        if (t.type === 'expense' || t.type === 'savings') {
            if (spentMap[t.categoryId] !== undefined) {
                spentMap[t.categoryId] += Number(t.amount);
            }
        }
    });
    return spentMap;
}
function updateMetrics() {
    let totalIncome = 0;
    let totalExpenses = 0;
    
    state.transactions.forEach(t => {
        const amt = Number(t.amount);
        if (t.type === 'income') {
            totalIncome += amt;
        } else if (t.type === 'expense' || t.type === 'savings') {
            totalExpenses += amt;
        }
    });
    
    // Calculate remaining budget: Total Budget Limit - Total Spent
    const spentMap = getCategorySpentMap();
    const allCats = getCategories();
    const expenseCats = allCats.filter(c => c.type === 'expense' || c.type === 'savings');
    
    let totalBudgetLimit = 0;
    let totalSpent = 0;
    
    expenseCats.forEach(cat => {
        totalBudgetLimit += (state.budgets[cat.id] || 0);
        totalSpent += (spentMap[cat.id] || 0);
    });
    
    const remainingBudget = totalBudgetLimit - totalSpent;
    
    document.getElementById('home-total-income').textContent = formatCurrency(totalIncome);
    document.getElementById('home-total-expenses').textContent = formatCurrency(totalExpenses);
    
    const balanceEl = document.getElementById('home-total-balance');
    balanceEl.textContent = formatCurrency(remainingBudget);
    
    if (remainingBudget < 0) {
        balanceEl.className = 'metric-value text-danger';
    } else {
        balanceEl.className = 'metric-value text-primary'; // Green main theme
    }
}
function renderRecentTransactions() {
    const listContainer = document.getElementById('home-recent-transactions');
    listContainer.innerHTML = '';
    
    const sorted = [...state.transactions].sort((a, b) => new Date(b.date) - new Date(a.date));
    const recent = sorted.slice(0, 4);
    
    if (recent.length === 0) {
        listContainer.innerHTML = '<div class="empty-state">No recent activity.</div>';
        return;
    }
    
    const allCats = getCategories();
    recent.forEach(t => {
        const cat = allCats.find(c => c.id === t.categoryId) || { color: '#6b7280', icon: 'help', name: 'Uncategorized' };
        const isIncome = t.type === 'income';
        const colorClass = isIncome ? 'text-success' : 'text-danger';
        const prefix = isIncome ? '+' : '-';
        
        const row = document.createElement('div');
        row.className = 'recent-item';
        row.innerHTML = `
            <div class="recent-info-block">
                <div class="recent-icon-wrapper" style="background-color: ${cat.color}15; color: ${cat.color};">
                    <span class="material-symbols-rounded">${cat.icon}</span>
                </div>
                <div class="recent-details">
                    <h4>${escapeHTML(t.title)}</h4>
                    <span>${formatDate(t.date)}</span>
                </div>
            </div>
            <div class="recent-amount-block">
                <span class="recent-amount ${colorClass}">${prefix}${formatCurrency(t.amount)}</span>
                <span class="recent-category-badge">${cat.name}</span>
            </div>
        `;
        listContainer.appendChild(row);
    });
}
function safeRenderChart(ctx, renderFn) {
    if (!ctx) return;
    if (typeof Chart === 'undefined') {
        const parent = ctx.parentElement;
        if (parent) {
            parent.innerHTML = `
                <div class="empty-state" style="padding: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%;">
                    <span class="material-symbols-rounded" style="font-size: 2.5rem; color: var(--text-muted); display: block; margin-bottom: 10px;">cloud_off</span>
                    <p style="font-size: 0.85rem; color: var(--text-secondary); font-weight: 500; text-align: center; line-height: 1.4;">
                        Chart.js failed to load.<br>Please connect to the internet to display interactive graphs.
                    </p>
                </div>
            `;
        }
        return;
    }
    renderFn(ctx);
}
function renderHomeDonutChart() {
    const ctx = document.getElementById('donutChart');
    safeRenderChart(ctx, (canvas) => {
        if (charts.donut) {
            charts.donut.destroy();
        }
        
        const spentMap = getCategorySpentMap();
        const allCats = getCategories();
        const expenseCats = allCats.filter(c => c.type === 'expense' || c.type === 'savings');
        
        let totalBudgetLimit = 0;
        let totalSpent = 0;
        
        expenseCats.forEach(cat => {
            totalBudgetLimit += (state.budgets[cat.id] || 0);
            totalSpent += (spentMap[cat.id] || 0);
        });
        
        const labels = [];
        const data = [];
        const colors = [];
        
        const remainingBudget = totalBudgetLimit - totalSpent;
        
        if (remainingBudget < 0) {
            // Over budget case
            labels.push('Total Spent (Over Budget!)');
            data.push(totalSpent);
            colors.push('#EF4444'); // Danger Red
        } else {
            // Under budget case
            labels.push('Total Spent');
            data.push(totalSpent);
            colors.push('#3B82F6'); // Accent Blue
            
            labels.push('Remaining Budget');
            data.push(remainingBudget);
            colors.push('#22C55E'); // Brand Green
        }
        
        if (totalBudgetLimit === 0 && totalSpent === 0) {
            labels.push('No Budget Set');
            data.push(1);
            colors.push('#CBD5E1');
        }
        
        charts.donut = new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#FFFFFF',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#1F2937',
                            font: { family: 'Outfit', size: 11 },
                            boxWidth: 10,
                            padding: 12
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const val = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                return ` ${context.label}: $${val.toFixed(2)} (${pct}%)`;
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    });
}
// ----------------------------------------------------
// ANALYTICS VIEW: STACKED BAR CHARTS
// ----------------------------------------------------
function renderAnalyticsCharts() {
    const expCtx = document.getElementById('expensesBarChart');
    const incCtx = document.getElementById('incomeBarChart');
    
    safeRenderChart(expCtx, (canvas) => {
        if (charts.expensesBar) charts.expensesBar.destroy();
        const months = state.analyticsData.months;
        const allCats = getCategories();
        const expCats = Object.keys(state.analyticsData.expenses);
        const expDatasets = expCats.map(catName => {
            const catObj = allCats.find(c => c.name === catName) || { color: '#6b7280' };
            return {
                label: catName,
                data: state.analyticsData.expenses[catName],
                backgroundColor: catObj.color,
                stack: 'expensesStack',
                borderRadius: 4
            };
        });
        
        charts.expensesBar = new Chart(canvas, {
            type: 'bar',
            data: {
                labels: months,
                datasets: expDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#1F2937', font: { family: 'Outfit', size: 11 } }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: { color: '#F1F5F9' },
                        ticks: { color: '#4B5563', font: { family: 'Outfit' } }
                    },
                    y: {
                        stacked: true,
                        grid: { color: '#F1F5F9' },
                        ticks: { color: '#4B5563', font: { family: 'Outfit' } }
                    }
                }
            }
        });
    });
    
    safeRenderChart(incCtx, (canvas) => {
        if (charts.incomeBar) charts.incomeBar.destroy();
        const months = state.analyticsData.months;
        const allCats = getCategories();
        const incCats = Object.keys(state.analyticsData.income);
        const incDatasets = incCats.map(catName => {
            const catObj = allCats.find(c => c.name === catName) || { color: '#6b7280' };
            return {
                label: catName,
                data: state.analyticsData.income[catName],
                backgroundColor: catObj.color,
                stack: 'incomeStack',
                borderRadius: 4
            };
        });
        
        charts.incomeBar = new Chart(canvas, {
            type: 'bar',
            data: {
                labels: months,
                datasets: incDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#1F2937', font: { family: 'Outfit', size: 11 } }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: { color: '#F1F5F9' },
                        ticks: { color: '#4B5563', font: { family: 'Outfit' } }
                    },
                    y: {
                        stacked: true,
                        grid: { color: '#F1F5F9' },
                        ticks: { color: '#4B5563', font: { family: 'Outfit' } }
                    }
                }
            }
        });
    });
    
    // Cumulative Expense Breakdown donut chart
    const donutCtx = document.getElementById('analyticsDonutChart');
    safeRenderChart(donutCtx, (canvas) => {
        if (charts.analyticsDonut) charts.analyticsDonut.destroy();
        
        const months = state.analyticsData.months;
        const allCats = getCategories();
        
        // Sum historical expenses from analyticsData
        const cumulativeSpent = {};
        let totalCumulativeSpent = 0;
        
        Object.keys(state.analyticsData.expenses).forEach(catName => {
            const arr = state.analyticsData.expenses[catName];
            const sum = arr.reduce((a, b) => a + b, 0);
            cumulativeSpent[catName] = sum;
            totalCumulativeSpent += sum;
        });
        
        const labels = [];
        const data = [];
        const colors = [];
        
        Object.keys(cumulativeSpent).forEach(catName => {
            const spent = cumulativeSpent[catName];
            if (spent > 0) {
                const catObj = allCats.find(c => c.name === catName) || { color: '#6b7280' };
                labels.push(catName);
                data.push(spent);
                colors.push(catObj.color);
            }
        });
        
        if (data.length === 0) {
            labels.push('No Expenses');
            data.push(1);
            colors.push('#CBD5E1');
        }
        
        charts.analyticsDonut = new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#FFFFFF',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#1F2937',
                            font: { family: 'Outfit', size: 11 },
                            boxWidth: 10,
                            padding: 12
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const val = context.raw;
                                const pct = totalCumulativeSpent > 0 ? ((val / totalCumulativeSpent) * 100).toFixed(1) : 0;
                                return ` ${context.label}: $${val.toFixed(2)} (${pct}%)`;
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    });
}
// ----------------------------------------------------
// BUDGET VIEW: CATEGORY CONSUMPTION
// ----------------------------------------------------
function renderBudgets() {
    const container = document.getElementById('budget-cards-container');
    if (!container) return;
    container.innerHTML = '';
    
    const spentMap = getCategorySpentMap();
    const allCats = getCategories();
    const expenseCats = allCats.filter(c => c.type === 'expense' || c.type === 'savings');
    
    let totalLimitSum = 0;
    let totalSpentSum = 0;
    
    expenseCats.forEach(cat => {
        const limit = state.budgets[cat.id] || 0;
        const spent = spentMap[cat.id] || 0;
        
        totalLimitSum += limit;
        totalSpentSum += spent;
        
        const pct = limit > 0 ? (spent / limit) * 100 : 0;
        const isOver = pct > 100;
        
        let progressClass = 'normal';
        if (pct >= 85 && pct <= 100) {
            progressClass = 'warning';
        } else if (pct > 100) {
            progressClass = 'danger';
        }
        
        const card = document.createElement('div');
        card.className = `card budget-card ${isOver ? 'over-budget' : ''}`;
        card.innerHTML = `
            <div class="budget-card-meta">
                <div class="budget-category-info">
                    <h4>${cat.name}</h4>
                    <span>${cat.type === 'savings' ? 'Savings allocation' : 'Expense limit'}</span>
                </div>
                <div class="budget-limit-display">
                    <span class="consumption-pct ${isOver ? 'text-danger' : ''}">${pct.toFixed(0)}%</span>
                    <span class="fraction">${formatCurrency(spent)} / ${formatCurrency(limit)}</span>
                </div>
            </div>
            
            <div class="progress-bar-container">
                <div class="progress-bar-fill ${progressClass}" style="width: ${Math.min(pct, 100)}%;"></div>
            </div>
            
            <div class="budget-card-actions">
                ${isOver ? '<span class="badge badge-danger" style="margin-right: auto;">OVER BUDGET!</span>' : ''}
                <button class="btn btn-secondary btn-icon-only edit-action" onclick="openBudgetModal('${cat.id}', '${cat.name}', ${limit})">
                    <span class="material-symbols-rounded" style="font-size: 16px;">edit</span>
                </button>
            </div>
        `;
        container.appendChild(card);
    });
    
    document.getElementById('budget-total-limit').textContent = formatCurrency(totalLimitSum);
    document.getElementById('budget-total-spent').textContent = formatCurrency(totalSpentSum);
}
window.openBudgetModal = function(catId, catName, currentLimit) {
    document.getElementById('budget-cat-id').value = catId;
    document.getElementById('budget-category-label').textContent = `Set limit for "${catName}" ($)`;
    document.getElementById('budget-limit-amount').value = currentLimit;
    
    const modal = document.getElementById('budget-modal');
    modal.showModal();
};
document.getElementById('budget-modal-close').addEventListener('click', () => {
    document.getElementById('budget-modal').close();
});
document.getElementById('budget-modal-cancel').addEventListener('click', () => {
    document.getElementById('budget-modal').close();
});
document.getElementById('budget-form').addEventListener('submit', (e) => {
    e.preventDefault();
    const catId = document.getElementById('budget-cat-id').value;
    const limitAmt = Number(document.getElementById('budget-limit-amount').value);
    
    if (catId && !isNaN(limitAmt)) {
        state.budgets[catId] = limitAmt;
        saveState();
        renderBudgets();
        document.getElementById('budget-modal').close();
    }
});
// ----------------------------------------------------
// GOALS VIEW: LINKED TO TRANSACTIONS & MANUAL BASES
// ----------------------------------------------------
function renderGoals() {
    const container = document.getElementById('goals-cards-container');
    if (!container) return;
    container.innerHTML = '';
    
    if (state.goals.length === 0) {
        container.innerHTML = '<div class="card" style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--text-muted);">No savings goals yet. Create one!</div>';
        return;
    }
    
    state.goals.forEach(goal => {
        const currentSaved = getGoalProgress(goal);
        const pct = goal.targetAmount > 0 ? (currentSaved / goal.targetAmount) * 100 : 0;
        
        const card = document.createElement('div');
        card.className = 'card goal-card';
        card.innerHTML = `
            <div class="goal-card-header">
                <div class="goal-title-block">
                    <h4>${escapeHTML(goal.title)}</h4>
                    <span>Savings Goal</span>
                </div>
                <div class="actions-cell-wrapper">
                    <button class="btn btn-icon-only edit-action" onclick="openGoalModal('${goal.id}')">
                        <span class="material-symbols-rounded" style="font-size: 16px;">edit</span>
                    </button>
                    <button class="btn btn-icon-only delete-action" onclick="deleteGoal('${goal.id}')">
                        <span class="material-symbols-rounded" style="font-size: 16px;">delete</span>
                    </button>
                </div>
            </div>
            
            <div class="goal-progress-gauge">
                <div class="progress-circular" style="--pct: ${Math.min(pct, 100).toFixed(0)};">
                    <span class="progress-circular-text">${pct.toFixed(0)}%</span>
                </div>
                <div class="goal-progress-details">
                    <span class="amount">${formatCurrency(currentSaved)}</span>
                    <span class="target">target: ${formatCurrency(goal.targetAmount)}</span>
                </div>
            </div>
            
            <div class="goal-quick-update">
                <input type="number" class="goal-quick-input" id="quick-deposit-${goal.id}" placeholder="Add savings amount ($)..." min="1" step="5">
                <button class="btn btn-primary" onclick="quickDepositToGoal('${goal.id}')">
                    Add Deposit
                </button>
            </div>
        `;
        container.appendChild(card);
    });
}
// Deposit directly links to transactions category automatically!
window.quickDepositToGoal = function(goalId) {
    const input = document.getElementById(`quick-deposit-${goalId}`);
    const amt = Number(input.value);
    
    if (isNaN(amt) || amt <= 0) {
        alert("Please enter a valid deposit amount.");
        return;
    }
    
    const goal = state.goals.find(g => g.id === goalId);
    if (!goal) return;
    
    // Add transaction under the goal's category
    const today = new Date().toISOString().split('T')[0];
    const newTrans = {
        id: 't_' + Date.now(),
        title: `Deposit for: ${goal.title}`,
        date: today,
        amount: amt,
        type: 'expense', // Evaluated as savings/expense
        inout: 'Out',
        categoryId: `goal_${goal.id}`
    };
    
    state.transactions.push(newTrans);
    saveState();
    
    input.value = ''; // Clean input
    renderGoals();
};
window.deleteGoal = function(goalId) {
    if (confirm("Are you sure you want to delete this savings goal? This will also remove any transactions associated with this goal.")) {
        // Remove transactions associated
        state.transactions = state.transactions.filter(t => t.categoryId !== `goal_${goalId}`);
        // Remove budget limits
        if (state.budgets[`goal_${goalId}`]) {
            delete state.budgets[`goal_${goalId}`];
        }
        // Remove goal
        state.goals = state.goals.filter(g => g.id !== goalId);
        saveState();
        renderGoals();
    }
};
// Goal Modal Actions
const goalModal = document.getElementById('goal-modal');
document.getElementById('add-goal-btn').addEventListener('click', () => {
    document.getElementById('goal-id').value = '';
    document.getElementById('goal-modal-title').textContent = 'Create Savings Goal';
    document.getElementById('goal-title').value = '';
    document.getElementById('goal-target').value = '';
    document.getElementById('goal-current').value = '0';
    document.getElementById('goal-budget-limit').value = '200';
    document.getElementById('goal-modal-submit').textContent = 'Create Goal';
    goalModal.showModal();
});
window.openGoalModal = function(goalId) {
    const goal = state.goals.find(g => g.id === goalId);
    if (!goal) return;
    
    document.getElementById('goal-id').value = goal.id;
    document.getElementById('goal-modal-title').textContent = 'Edit Savings Goal';
    document.getElementById('goal-title').value = goal.title;
    document.getElementById('goal-target').value = goal.targetAmount;
    document.getElementById('goal-current').value = goal.startingAmount;
    document.getElementById('goal-budget-limit').value = state.budgets[`goal_${goal.id}`] || '200';
    document.getElementById('goal-modal-submit').textContent = 'Save Goal';
    goalModal.showModal();
};
document.getElementById('goal-modal-close').addEventListener('click', () => goalModal.close());
document.getElementById('goal-modal-cancel').addEventListener('click', () => goalModal.close());
document.getElementById('goal-form').addEventListener('submit', (e) => {
    e.preventDefault();
    const id = document.getElementById('goal-id').value;
    const title = document.getElementById('goal-title').value;
    const target = Number(document.getElementById('goal-target').value);
    const starting = Number(document.getElementById('goal-current').value);
    const budgetLimit = Number(document.getElementById('goal-budget-limit').value);
    
    if (id) {
        // Edit Goal
        const idx = state.goals.findIndex(g => g.id === id);
        if (idx !== -1) {
            state.goals[idx].title = title;
            state.goals[idx].targetAmount = target;
            state.goals[idx].startingAmount = starting;
            state.budgets[`goal_${id}`] = budgetLimit;
        }
    } else {
        // Create Goal
        const newGoal = {
            id: 'g' + Date.now(),
            title,
            targetAmount: target,
            startingAmount: starting
        };
        state.goals.push(newGoal);
        state.budgets[`goal_${newGoal.id}`] = budgetLimit;
    }
    
    saveState();
    renderGoals();
    goalModal.close();
});
// ----------------------------------------------------
// TRANSACTION VIEW: CRUD & FILTERING
// ----------------------------------------------------
function populateFormCategoryDropdown() {
    const drop = document.getElementById('trans-category');
    if (!drop) return;
    drop.innerHTML = '';
    
    const allCats = getCategories();
    allCats.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.id;
        option.textContent = `${cat.name} (${cat.type === 'savings' ? 'Savings' : cat.type})`;
        drop.appendChild(option);
    });
    
    const filterCatDrop = document.getElementById('filter-category');
    if (filterCatDrop) {
        filterCatDrop.innerHTML = '<option value="all">All Categories</option>';
        allCats.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.id;
            option.textContent = cat.name;
            filterCatDrop.appendChild(option);
        });
    }
}
function renderTransactionsList() {
    const rowsContainer = document.getElementById('transaction-rows');
    const emptyState = document.getElementById('transactions-empty-state');
    
    if (!rowsContainer) return;
    rowsContainer.innerHTML = '';
    
    const query = document.getElementById('transaction-search').value.toLowerCase();
    const typeFilter = document.getElementById('filter-type').value;
    const catFilter = document.getElementById('filter-category').value;
    
    const allCats = getCategories();
    
    const filtered = state.transactions.filter(t => {
        const matchesSearch = t.title.toLowerCase().includes(query);
        const matchesType = typeFilter === 'all' || t.type === typeFilter;
        const matchesCat = catFilter === 'all' || t.categoryId === catFilter;
        return matchesSearch && matchesType && matchesCat;
    });
    
    filtered.sort((a, b) => new Date(b.date) - new Date(a.date));
    
    if (filtered.length === 0) {
        emptyState.style.display = 'block';
        return;
    }
    emptyState.style.display = 'none';
    
    filtered.forEach(t => {
        const cat = allCats.find(c => c.id === t.categoryId) || { name: 'Uncategorized' };
        const isIncome = t.type === 'income';
        const colorClass = isIncome ? 'text-success' : 'text-danger';
        
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="transaction-title-cell">${escapeHTML(t.title)}</td>
            <td>${formatDate(t.date)}</td>
            <td class="recent-amount ${colorClass}">${isIncome ? '+' : '-'}${formatCurrency(t.amount)}</td>
            <td>
                <span class="badge ${isIncome ? 'badge-success' : 'badge-accent'}">
                    ${t.type.toUpperCase()}
                </span>
            </td>
            <td>${t.inout}</td>
            <td>${cat.name}</td>
            <td class="text-right">
                <div class="actions-cell-wrapper">
                    <button class="btn btn-icon-only edit-action" onclick="openTransactionModal('${t.id}')">
                        <span class="material-symbols-rounded" style="font-size: 16px;">edit</span>
                    </button>
                    <button class="btn btn-icon-only delete-action" onclick="deleteTransaction('${t.id}')">
                        <span class="material-symbols-rounded" style="font-size: 16px;">delete</span>
                    </button>
                </div>
            </td>
        `;
        rowsContainer.appendChild(tr);
    });
}
document.getElementById('transaction-search').addEventListener('input', renderTransactionsList);
document.getElementById('filter-type').addEventListener('change', renderTransactionsList);
document.getElementById('filter-category').addEventListener('change', renderTransactionsList);
const transModal = document.getElementById('transaction-modal');
document.getElementById('header-add-transaction-btn').addEventListener('click', () => {
    populateFormCategoryDropdown();
    document.getElementById('trans-id').value = '';
    document.getElementById('transaction-modal-title').textContent = 'Add Transaction';
    document.getElementById('trans-title').value = '';
    
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('trans-date').value = today;
    
    document.getElementById('trans-amount').value = '';
    document.getElementById('trans-type').value = 'expense';
    document.getElementById('trans-inout').value = 'Out';
    
    const firstExp = state.baseCategories.find(c => c.type === 'expense');
    if (firstExp) {
        document.getElementById('trans-category').value = firstExp.id;
    }
    
    document.getElementById('transaction-modal-submit').textContent = 'Save Transaction';
    transModal.showModal();
});
window.openTransactionModal = function(transId) {
    populateFormCategoryDropdown();
    const t = state.transactions.find(x => x.id === transId);
    if (!t) return;
    
    document.getElementById('trans-id').value = t.id;
    document.getElementById('transaction-modal-title').textContent = 'Edit Transaction';
    document.getElementById('trans-title').value = t.title;
    document.getElementById('trans-date').value = t.date;
    document.getElementById('trans-amount').value = t.amount;
    document.getElementById('trans-type').value = t.type;
    document.getElementById('trans-inout').value = t.inout;
    document.getElementById('trans-category').value = t.categoryId;
    document.getElementById('transaction-modal-submit').textContent = 'Save Changes';
    
    transModal.showModal();
};
document.getElementById('transaction-modal-close').addEventListener('click', () => transModal.close());
document.getElementById('transaction-modal-cancel').addEventListener('click', () => transModal.close());
document.getElementById('transaction-form').addEventListener('submit', (e) => {
    e.preventDefault();
    const id = document.getElementById('trans-id').value;
    const title = document.getElementById('trans-title').value;
    const date = document.getElementById('trans-date').value;
    const amount = Number(document.getElementById('trans-amount').value);
    const type = document.getElementById('trans-type').value;
    const inout = document.getElementById('trans-inout').value;
    const categoryId = document.getElementById('trans-category').value;
    
    if (id) {
        const idx = state.transactions.findIndex(x => x.id === id);
        if (idx !== -1) {
            state.transactions[idx] = { id, title, date, amount, type, inout, categoryId };
        }
    } else {
        const newTrans = {
            id: 't_' + Date.now(),
            title,
            date,
            amount,
            type,
            inout,
            categoryId
        };
        state.transactions.push(newTrans);
    }
    
    saveState();
    
    const hash = window.location.hash || '#home';
    renderActiveView(hash.substring(1));
    updateMetrics();
    
    transModal.close();
});
window.deleteTransaction = function(transId) {
    if (confirm("Delete this transaction permanently?")) {
        state.transactions = state.transactions.filter(t => t.id !== transId);
        saveState();
        
        const hash = window.location.hash || '#home';
        renderActiveView(hash.substring(1));
        updateMetrics();
    }
};
document.getElementById('trans-type').addEventListener('change', (e) => {
    const val = e.target.value;
    const inoutDrop = document.getElementById('trans-inout');
    const catDrop = document.getElementById('trans-category');
    
    if (val === 'income') {
        inoutDrop.value = 'In';
        catDrop.value = 'salary';
    } else {
        inoutDrop.value = 'Out';
        catDrop.value = 'house';
    }
});
// ----------------------------------------------------
// UTILITIES
// ----------------------------------------------------
function formatCurrency(num) {
    return '$' + Number(num).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}
function formatDate(dateStr) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    const dateObj = new Date(dateStr);
    const userTimezoneOffset = dateObj.getTimezoneOffset() * 60000;
    const adjustedDate = new Date(dateObj.getTime() + userTimezoneOffset);
    return adjustedDate.toLocaleDateString('en-US', options);
}
function escapeHTML(str) {
    return str.replace(/[&<>'"]/g, 
        tag => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            "'": '&#39;',
            '"': '&quot;'
        }[tag] || tag)
    );
}
// ----------------------------------------------------
// APPLICATION BOOTSTRAP
// ----------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
    loadState();
    
    // Bind routing events
    window.addEventListener('hashchange', handleRouting);
    
    // Initial routing load
    handleRouting();
    
});





<!-- Add Transaction Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1" role="dialog"
    aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionModalLabel">Add Transaction</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="transactionForm" method="POST" action="{{ route('transactions.store') }}"
                    data-user-salary="{{ auth()->check() ? (auth()->user()->salary ?? '') : '' }}">
                    @csrf
                    <div class="form-group">
                        <label for="transTitle">Description / Title</label>
                        <input type="text" class="form-control" id="transTitle" name="title"
                            placeholder="e.g. Monthly Rent" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="transDate">Date</label>
                            <input type="date" class="form-control" id="transDate" name="date" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="transAmount">Amount ($)</label>
                            <input type="number" class="form-control" id="transAmount" name="amount" min="0.01"
                                step="0.01" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="transType">Type</label>
                        <select class="form-control" id="transType" name="type">
                            <option value="expense">Expense</option>
                            <option value="income">Income</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="transCategory">Category</label>
                        <select class="form-control" id="transCategory" name="category">
                            <option>House</option>
                            <option>Credit Card</option>
                            <option>Transportation</option>
                            <option>Groceries</option>
                            <option>Shopping</option>
                            <option>General Savings</option>
                            <option>Salary</option>
                            <option>Bonus</option>
                        </select>
                        <small class="form-text text-muted">Choosing a "Goal:" category will count toward that
                            savings goal's progress.</small>
                    </div>

                    @php
                        $userGoals = auth()->check() ? auth()->user()->goals()->get() : collect();
                    @endphp
                    <div class="form-group">
                        <label for="transGoal">Deposit into Savings Goal (optional)</label>
                        <select class="form-control" id="transGoal" name="goal_id">
                            <option value="">— None —</option>
                            @foreach ($userGoals as $goal)
                                <option value="{{ $goal->id }}">{{ $goal->name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Choosing a goal will add this amount to that goal's
                            saved progress.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" type="submit" form="transactionForm">Save Transaction</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('transactionForm');
        var categorySelect = document.getElementById('transCategory');
        var typeSelect = document.getElementById('transType');
        var amountInput = document.getElementById('transAmount');

        if (form && categorySelect && amountInput) {
            categorySelect.addEventListener('change', function () {
                if (categorySelect.value === 'Salary') {
                    var salary = form.dataset.userSalary;
                    if (salary) {
                        amountInput.value = parseFloat(salary).toFixed(2);
                    }
                    if (typeSelect) {
                        typeSelect.value = 'income';
                    }
                }
            });
        }
    });
</script>
<!-- Create Savings Goal Modal -->
<div class="modal fade" id="createGoalModal" tabindex="-1" role="dialog"
    aria-labelledby="createGoalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createGoalModalLabel">Create Savings Goal</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createGoalForm" method="POST" action="{{ route('goals.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="goalName">Goal Name</label>
                        <input type="text" class="form-control" id="goalName" name="name"
                            placeholder="e.g. New Laptop" required>
                    </div>
                    <div class="form-group">
                        <label for="goalTarget">Target Amount ($)</label>
                        <input type="number" class="form-control" id="goalTarget" name="target_amount"
                            min="0.01" step="0.01" placeholder="0.00" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" type="submit" form="createGoalForm">Create Goal</button>
            </div>
        </div>
    </div>
</div>
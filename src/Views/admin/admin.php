<div class="container my-5">
    <!-- Dashboard Header -->
    <div class="row my-5">
        <div class="col-md-12">
            <h1 class="text-center text-white">Admin Dashboard</h1>
        </div>
    </div>

    <!-- Users Panel -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card fixed-height">
                <div class="card-header">
                    <h4>Manage Users</h4>
                </div>
                <div class="card-body overflow-auto">
                    <table class="table table-hover">
                    <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Username</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Admin</th>
                                <th class="text-center">Active</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)) : ?>
                                <?php foreach ($users as $index => $user) : ?>
                                    <tr>
                                        <td class="text-center"><?= $index + 1 ?></td>
                                        <td class="text-center"><?= htmlspecialchars($user['username']) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($user['email']) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($user['is_admin']) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($user['active']) ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-danger">Ban</button>
                                            <button class="btn btn-sm btn-warning">Edit</button>
                                            <button class="btn btn-sm btn-success">Activate</button>
                                            <button class="btn btn-sm btn-info">Promote</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5" class="text-center">No users found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tournament Panel -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card fixed-height">
                <div class="card-header d-flex justify-content-between align-items-center pb-2">
                    <h4>Manage Tournaments</h4>
                    <!-- Bottone per aprire la modale -->
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTournamentModal">
                        Create New Tournament
                    </button>
                </div>
                <div class="card-body overflow-auto">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Start Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tournaments)) : ?>
                                <?php foreach ($tournaments as $index => $tournament) : ?>
                                    <tr>
                                        <td class="text-center"><?= $index + 1 ?></td>
                                        <td class="text-center"><?= htmlspecialchars($tournament['name']) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($tournament['status']) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($tournament['start_date']) ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-info tournView"  data-id="<?= $tournament['id'] ?>">View</button>
                                            <button class="btn btn-sm btn-danger tournDel" data-id="<?= $tournament['id'] ?>">Delete</button>
                                            <button class="btn btn-sm btn-success tournAct" data-id="<?= $tournament['id'] ?>">Activate</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5" class="text-center">No tournaments found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- System Logs Panel -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card fixed-height">
                <div class="card-header">
                    <h4>System Logs</h4>
                </div>
                <div class="card-body overflow-auto">
                    <pre class="bg-light p-3">
                        [2024-05-10 12:34:56] INFO: User JohnDoe logged in.
                        [2024-05-10 12:45:22] ERROR: Tournament creation failed due to missing data.
                        [2024-05-10 13:00:00] INFO: Match between JohnDoe and JaneSmith recorded.
                    </pre>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Create Tournament Modal -->
<div class="modal fade" id="createTournamentModal" tabindex="-1" aria-labelledby="createTournamentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTournamentModalLabel">Create New Tournament</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="tournamentForm" action="/admin/tournaments/create" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="tournamentName" class="form-label">Tournament Name</label>
                        <input type="text" class="form-control" id="tournamentName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="tournamentDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="tournamentDescription" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDate" name="start_date" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitTournamentBtn">Create</button>
            </div>
        </div>
    </div>
</div>

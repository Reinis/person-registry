<h1>Person Registry</h1>
<hr>
<div>
    <div class="input-group">
        <?php $searchField = $searchField ?? 'name' ?>
        <form action="/search" method="post">
            <select name="searchField">
                <option value="name" <?php if ($searchField === 'name') echo 'selected'; ?>>Name</option>
                <option value="nid" <?php if ($searchField === 'nid') echo 'selected'; ?>>National Id</option>
                <option value="notes" <?php if ($searchField === 'notes') echo 'selected'; ?>>Notes</option>
                <option value="all" <?php if ($searchField === 'all') echo 'selected'; ?>>All</option>
            </select>
            <input type="search" name="searchTerm" placeholder="Search">
            <input type="submit" name="search" value="Search">
        </form>
    </div>
    <a href="/add">
        <button>Add New</button>
    </a>
    <table>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>National Id</th>
            <th>Notes</th>
            <th>Action</th>
        </tr>
        <?php foreach ($people as $person): ?>
            <tr>
                <td><?= $person->getId() ?></td>
                <td><?= $person->getName() ?></td>
                <td><?= $person->getNationalId() ?></td>
                <td><?= $person->getNotes() ?></td>
                <td>
                    <div class="btn-group">
                        <form action="/delete/<?= $person->getId() ?>" method="post">
                            <input type="submit" value="Delete" class="button">
                        </form>
                        <a href="/edit/<?= $person->getId() ?>">
                            <button class="button">Edit</button>
                        </a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
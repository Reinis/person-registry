<h1>Person Registry</h1>
<hr>
<div>
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
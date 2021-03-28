<div>
    <form action="/edit/<?= $person->getId() ?>" method="post">
        <label for="fist_name">First Name:</label>
        <input type="text" id="fist_name" value="<?= $person->getFirstName() ?>" disabled>
        <br>
        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" value="<?= $person->getLastName() ?>" disabled>
        <br>
        <label for="nid">National Id:</label>
        <input type="text" id="nid" value="<?= $person->getNationalId() ?>" disabled>
        <br>
        <label for="notes">Notes:</label>
        <input type="text" name="notes" id="notes" value="<?= $person->getNotes() ?>">
        <br>
        <div class="btn-group">
            <a href="/"><input type="button" class="button" value="Back"></a>
            <input type="submit" class="button" value="Submit">
        </div>
    </form>
</div>

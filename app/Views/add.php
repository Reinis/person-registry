<div>
    <form action="/add" method="post">
        <label for="fist_name">First Name:</label>
        <input type="text" id="fist_name" placeholder="Name" name="first_name">
        <br>
        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" placeholder="Surname" name="last_name">
        <br>
        <label for="nid">National Id:</label>
        <input type="text" id="nid" placeholder="000000-00000" name="nid">
        <br>
        <label for="notes">Notes:</label>
        <input type="text" id="notes" name="notes" value="">
        <br>
        <div class="btn-group">
            <a href="/"><input type="button" class="button" value="Back"></a>
            <input type="submit" class="button" value="Submit">
        </div>
    </form>
</div>

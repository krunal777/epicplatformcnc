<!-- resources/views/contact.blade.php -->

<form method="post" action="/api/pricesupdate"  enctype="multipart/form-data">
    @csrf
    <input type="file" name="file" ><br>
    <input type="password" name="password" placeholder="Your password"><br>
    <button type="submit">Submit</button>
</form>

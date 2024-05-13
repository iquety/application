<html>
    <body>
        <form action="#" method=post enctype="multipart/form-data">
            <input type="file" name=single>
            <input type="file" name=any[] multiple>
            <input type="submit" value="vai">
        </form>

        <pre>
            <?php
                var_dump($_FILES);
                var_dump($_POST);
                var_dump($_SERVER);
            ?>
        </pre>
    </body>
</html>
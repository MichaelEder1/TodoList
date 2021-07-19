<?php
session_start();
/**
 * includes all PHP-class files, which are needed in this file automatically
 */
spl_autoload_register("autoload");
function autoload(string $className): void
{
    require_once($className . ".php");
}

//loads db credentials file
Database::loadConfig("config_inc.php");
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/styles.css">
    <title>TodoList</title>
</head>
<body>
<?php
$errors = [];

$query = "SELECT COUNT(userid) FROM user";
$result = Database::selectQuery($query);
echo $result->fetch_assoc()["COUNT(userid)"];
//=========================================//
//      login and registration logic       //
//=========================================//

if (!isset($_SESSION["user"]))
{
    //case: user not logged in
    if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "login")
    {
        //case: user wants to login
        if (isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && checkLogin($_REQUEST["username"], $_REQUEST["password"]))
        {
            //case: data correct - save user into session
            $_SESSION["user"] = array("username" => $_REQUEST["username"], "userId" => getUserId($_REQUEST["username"]), "role" => getuserRole($_REQUEST["username"]));
            showTodoForm();
            showTodoList();
        }
        else
        {
            //case: data incorrect
            echo("<span class='text-xl font-semibold text-indigo-700 text-center flex justify-center bg-gray-50'><em>Login failed</em></span>");
            showLoginForm();
            showRegistrationForm();
        }
    }
    else
    {
        if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "register")
        {
            //case: user wants to register
            if (checkRegistration() && register($_REQUEST["usernameReg"], $_REQUEST["passwordReg1"], $_REQUEST["email"]))
            {
                //data correct
                echo("<span class='text-xl text-indigo-700 font-semibold text-center flex justify-center bg-gray-50'><em>User '" . $_REQUEST["usernameReg"] . "' successfully registered! You can now login.</em></span>");
                showLoginForm();
                showRegistrationForm();
            }
            else
            {
                //case: data incorrect
                echo "<span class='text-xl text-indigo-700 font-semibold text-center flex justify-center bg-gray-50'> <em>User '" . $_REQUEST["usernameReg"] . "' could not be registered! username already taken. Please try another one.</em></span>";
                showRegistrationForm();
                showLoginForm();
            }
        }
        else
        {
            showLoginForm();
            showRegistrationForm();
        }
    }
}
else
{
    //case: user is logged in
    if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "logout")
    {
        //case: user wants to logout
        unset($_SESSION["user"]);
        echo "<span class='text-xl text-indigo-700 font-semibold text-center flex justify-center bg-gray-50'><em>You have been logged out</em></span>";
        showLoginForm();
        showRegistrationForm();
    }
    //add todoListItem
    elseif (isset($_REQUEST["action"]) && $_REQUEST["action"] == "addTodo")
    {
        if (isset($_REQUEST["newTaskInputTitle"]))
        {
            addTodo();
        }
        showTodoForm();
        showTodoList();
    }
    //delete todoListItem
    elseif (isset($_REQUEST["action"]) && $_REQUEST["action"] == "deleteTodo")
    {
        if (isset($_REQUEST["id"]))
        {
            deleteTask($_REQUEST["id"], $_SESSION["user"]["userId"]);
        }
        showTodoForm();
        showTodoList();
    }
    //change status of a todoListItem
    elseif (isset($_REQUEST["action"]) && $_REQUEST["action"] == "changeStatus")
    {
        if (isset($_REQUEST["id"]))
        {
            changeStatus($_REQUEST["id"], $_SESSION["user"]["userId"]);
        }
        showTodoForm();
        showTodoList();
    }
    //editTodoListItem
    elseif (isset($_REQUEST["action"]) && $_REQUEST["action"] == "updateTodo" && isset($_REQUEST["taskId"]) && isset($_REQUEST["newTaskInputTitle"]))
    {
        if (isset($_REQUEST["newTaskInputText"]))
        {
            editTodo($_REQUEST["taskId"], $_REQUEST["newTaskInputTitle"], $_REQUEST["newTaskInputText"]);
        }
        else
        {
            editTodo($_REQUEST["taskId"], $_REQUEST["newTaskInputTitle"]);
        }
        showTodoForm();
        showTodoList();
    }
    else
    {
        //case: user just wants to see the site
        showTodoForm();
        showTodoList();
    }
}

//=========================================//
//             checkLogin()                //
//=========================================//

/**
 * This function checks, if the given username and password match with an entry in database
 * @param string $username the username of the user who wants to login
 * @param string $password the password of the user who wants to login
 * @return bool true, if username and password match, else false
 */
function checkLogin(string $username, string $password): bool
{
    $result = Database::selectQuery("SELECT username, password FROM user WHERE username='" . $username . "';");
    if ($result->num_rows > 0)
    {
        if (password_verify($password, $result->fetch_assoc()['password']))
        {
            return true;
        }
        return false;
    }
    return false;
}

//=========================================//
//              getUserId()                //
//=========================================//

/**
 * This function returns the userid for a given username
 * @param $username string the username for which the userid should be searched for
 * @return int the userid, if no user was found -1 will be returned
 */
function getUserId(string $username): int
{
    $selectQuery = "SELECT userid FROM user WHERE username='" . $username . "';";
    $result = Database::selectQuery($selectQuery);
    if ($row = $result->fetch_assoc())
    {
        return $row["userid"];
    }
    return -1;
}

//=========================================//
//              getUserRole()              //
//=========================================//

/**
 * This functions returns the user role for a given username
 * @param $username string the username for which the user role should be searched for
 * @return int the user role, if no user was found -1 will be returned
 */
function getUserRole(string $username): int
{
    $selectQuery = "SELECT role FROM user WHERE username='" . $username . "';";
    $result = Database::selectQuery($selectQuery);
    if ($row = $result->fetch_assoc())
    {
        return $row["role"];
    }
    return -1;
}

//=========================================//
//          checkRegistration()            //
//=========================================//

/**
 *  This function checks the input from registration form on validity.
 * @return bool true = everything's fine / false = at least one mistakes
 */
function checkRegistration(): bool
{
    global $errors;
    //username
    if (!isset($_REQUEST["usernameReg"]) || strlen($_REQUEST["usernameReg"]) < 4)
    {
        $errors["usernameReg"] = "Username must be at least 4 characters long!";
    }
    //password
    if ((!isset($_REQUEST["passwordReg1"]) && !isset($_REQUEST["repeat_passwordReg2"])) || $_REQUEST["passwordReg1"] != $_REQUEST["repeat_passwordReg2"] || strlen($_REQUEST["passwordReg1"]) < 8)
    {
        $errors["passwordReg1"] = "Password must be at least 8 characters long and password must match!";
    }

    //e-mail
    if (!isset($_REQUEST["email"]) || filter_var($_REQUEST["email"], FILTER_VALIDATE_EMAIL) === false)
    {
        $errors["email"] = "Must be a valid email address!";
    }

    if (count($errors) > 0)
    {
        return false;
    }
    return true;
}

//=========================================//
//              register()                 //
//=========================================//

/**
 * This function registers an user in the database
 * @param string $username username of the new registered user
 * @param string $password password of the new registered user
 * @param string $mail mail address of the new registered user
 * @return bool true if registration was successful, else false
 */
function register(string $username, string $password, string $mail): bool
{
    if (usernameAvailable($username))
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $insertQuery = "INSERT into user VALUES (NULL, '" . $username . "', '" . $hashedPassword . "', '" . $mail . "', 1);";

        $id = Database::insertQuery($insertQuery);
        if ($id != 0)
        {
            return true;
        }
        return false;
    }
    return false;
}

//=========================================//
//            showLoginForm()              //
//=========================================//

/**
 * shows the login form
 */
function showLoginForm()
{
    ?>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8" id="login">
        <div class="max-w-md w-full space-y-8">
            <div>
                <img class="mx-auto h-24 w-auto" src="https://api.iconify.design/vscode-icons:file-type-light-todo.svg"
                     alt="Login TodoList">
                <h2 class="mt-6 text-center text-6xl font-extrabold text-gray-900">Login</h2>
            </div>
            <form class="mt-8 space-y-6" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" autocomplete="on">
                <input type="hidden" name="remember" value="true">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="username" class="sr-only">Username</label>
                        <input id="username" name="username" type="text" autocomplete="username" required
                               class="inputField rounded-t-md"
                               placeholder="Username">
                    </div>
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="inputField rounded-b-md"
                               placeholder="Password">
                    </div>
                </div>
                <div>
                    <button type="submit"
                            class="button">
                        Login
                    </button>
                </div>
                <p class="mt-4 text-xl font-bold">Don't have an account? <a
                            class="no-underline text-indigo-700 hover:text-indigo-500" href="#register">Register</a></p>
                <input type="hidden" name="action" value="login">
            </form>
        </div>
    </div>
<?php }

//=========================================//
//         showRegistrationForm()          //
//=========================================//

/**
 * shows the registration form
 */
function showRegistrationForm()
{
    global $errors;
    $username = "";
    $mail = "";

    if (isset($_REQUEST["usernameReg"]) && count($errors) > 0)
    {
        $username = $_REQUEST["usernameReg"];
    }
    if (isset($_REQUEST["email"]) && count($errors) > 0)
    {
        $mail = $_REQUEST["email"];
    }
    ?>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8" id="register">
        <div class="max-w-md w-full space-y-8">
            <div>
                <img class="mx-auto h-24 w-auto" src="https://api.iconify.design/vscode-icons:file-type-light-todo.svg"
                     alt="Login TodoList">
                <h2 class="mt-6 text-center text-6xl font-extrabold text-gray-900">Register</h2>
            </div>
            <form class="mt-8 space-y-6" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" autocomplete="on">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="email" class="sr-only">E-Mail Address</label>
                        <?php
                        if (isset($errors['email']))
                        {
                            ?>
                            <input id="email" name="email" type="text" autocomplete="current-password" required
                                   class="inputField rounded-t-md border-4 border-indigo-500" placeholder="E-Mail"
                                   value="<?php echo $mail ?>">
                            <?php
                            echo "<span class='text-red-600 text-semibold'>" . $errors["email"] . "</span><br>";
                        }
                        else
                        {
                            ?>
                            <input id="email" name="email" type="text" autocomplete="current-password" required
                                   class="inputField rounded-t-md"
                                   placeholder="E-Mail"><?php
                        }
                        ?>
                    </div>
                    <div>
                        <label for="usernameReg" class="sr-only">Username</label>
                        <?php
                        if (isset($errors['usernameReg']))
                        {
                            ?>
                            <input id="usernameReg" name="usernameReg" type="text" autocomplete="username" required
                                   class="inputField border 4 border-indigo-500" value="<?php echo $username ?>"
                                   placeholder="Username">
                            <?php
                            echo "<span class='text-red-600 text-semibold'>" . $errors["usernameReg"] . "</span><br>";
                        }
                        else
                        {
                            ?>
                            <input id="usernameReg" name="usernameReg" type="text" autocomplete="username" required
                                   class="inputField"
                                   placeholder="Username"><?php
                        }
                        ?>
                    </div>
                    <div>
                        <label for="passwordReg1" class="sr-only">Password</label>
                        <?php
                        if (isset($errors['passwordReg1']))
                        {
                            ?>
                            <input id="passwordReg1" name="passwordReg1" type="password" autocomplete="current-password"
                                   required class="inputField border-4 border-indigo-500" placeholder="Password">
                            <?php
                            echo "<span class='text-red-600 text-semibold'>" . $errors["passwordReg1"] . "</span><br>";
                        }
                        else
                        {
                            ?>
                            <input id="passwordReg1" name="passwordReg1" type="password" autocomplete="current-password"
                                   required class="inputField" placeholder="Password"><?php
                        }
                        ?>
                    </div>
                    <div>
                        <label for="repeat_passwordReg2" class="sr-only">Repeat Password</label>
                        <input id="repeat_passwordReg2" name="repeat_passwordReg2" type="password"
                               autocomplete="current-password" required
                               class="inputField rounded-b-md"
                               placeholder="Repeat Password">
                    </div>
                </div>
                <div>
                    <button type="submit"
                            class="button">
                        Register
                    </button>
                </div>
                <p class="mt-4 text-xl font-bold">Already have an account? <a
                            class="no-underline text-indigo-700 hover:text-indigo-500" href="#login">Login</a></p>
                <input type="hidden" name="action" value="register">
            </form>
        </div>
    </div>
<?php }

//=========================================//
//               showTodoForm()                //
//=========================================//

/**
 * shows todoForm
 */
function showTodoForm()
{
    $username = "";
    if ($_SESSION["user"]["username"])
    {
        $username = $_SESSION["user"]["username"];
    }
    ?>
    <div class="flex justify-end items-center flex-row bg-gray-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 items-center mr-2 -mt-2 fill-current text-indigo-700"
             viewBox="0 0 20 20" fill="">
            <path fill-rule="evenodd"
                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"
                  clip-rule="evenodd"/>
        </svg>
        <p class="text-lg font-semibold mr-3">Signed in as <span
                    class="text-lg font-semibold"><?php echo $username; ?></span></p>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="submit" value="Logout" class="button">
            <input type="hidden" value="logout" name="action">
        </form>
    </div>
    <div class="flex justify-center bg-gray-50 py-4 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <h2 class="text-center text-4xl font-bold text-indigo-700 mb-8">My todos</h2>
            <!-- Task input field -->
            <?php
            if (isset($_REQUEST["id"]))
            {
            ?>
            <form id="newTask" action="<?php echo $_SERVER['PHP_SELF']; ?>"
                  method="post"
                  class="mb-4">
                <input type="hidden" value="<?php echo $_REQUEST["id"]; ?>" name="taskId">
                <?php
                }
                else
                {
                ?>
                <form id="newTask" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post"
                      class="mb-4">
                    <?php
                    }
                    ?>
                    <div class="relative text-gray-700 mt-4">
                        <label class="block uppercase" for="newTaskInputTitle">task title<sup
                                    class="text-red-600">*</sup>
                        </label>
                        <?php
                        if (isset($_REQUEST["title"]))
                        {
                            ?>
                            <input
                            class="w-full h-10 pl-3 pr-8 text-base placeholder-gray-600 rounded-lg focus:shadow-outline"
                            id="newTaskInputTitle" name="newTaskInputTitle" type="text"
                            value="<?php echo $_REQUEST["title"] ?>"
                            required><?php
                        }
                        else
                        { ?>
                            <input class="w-full h-10 pl-3 pr-8 text-base placeholder-gray-600 rounded-lg focus:shadow-outline"
                                   id="newTaskInputTitle" name="newTaskInputTitle" type="text"
                                   placeholder="new task ..."
                                   required
                            /><?php } ?>
                    </div>
                    <div class="relative text-gray-700 mt-6">
                        <label class="block uppercase" for="newTaskInputText">Task description <span
                                    class="italic">(optional)</span>
                        </label>
                        <?php
                        if (isset($_REQUEST["text"]))
                        {
                            ?>
                            <textarea
                                    class="w-full h-20 pl-3 pr-8 text-base placeholder-gray-600 rounded-lg focus:shadow-outline"
                                    rows="3" name="newTaskInputText"
                                    id="newTaskInputText"><?php echo $_REQUEST["text"] ?></textarea>
                            <?php
                        }
                        else
                        { ?>
                            <textarea
                                    class="w-full h-20 pl-3 pr-8 text-base placeholder-gray-600 rounded-lg focus:shadow-outline"
                                    rows="3" name="newTaskInputText" id="newTaskInputText"
                                    placeholder="some description ..."></textarea>
                        <?php } ?>
                    </div>
                    <?php
                    if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "editTodo")
                    {
                        ?>
                        <button type="submit" class="button">update todo</button>
                        <input type="hidden" name="action" value="updateTodo">
                        <?php
                    }
                    else
                    {
                        ?>
                        <button type="submit" class="button">add task</button>
                        <input type="hidden" name="action" value="addTodo">
                        <?php
                    }
                    ?>
                </form>
                <!-- End new task field -->

                <!-- Divider between form and entries -->
                <div class="divide-y-4 divide-indigo-700 mb-3">
                    <div></div>
                    <div></div>
                </div>
        </div>
    </div>
    <?php
}

//=========================================//
//            showTodoList()               //
//=========================================//

/**
 * This functions shows the todoList based on the logged in user
 */
function showTodoList()
{
    echo '<div class="flex justify-center bg-gray-50 py-4 px-4 sm:px-6 lg:px-8">';
    echo '<div class="max-w-md w-full">';
    if ($_SESSION["user"]["role"] != 2)
    {
        //only show tasks, which belongs to logged in user
        $selectQuery = "SELECT * FROM todo WHERE userId ='" . $_SESSION["user"]["userId"] . "';";
    }
    else
    {
        //if admin role, show all todos
        $selectQuery = "SELECT * FROM todo;";
    }
    $result = Database::selectQuery($selectQuery);
    while ($row = $result->fetch_assoc())
    {
        if (isset($row["todoText"]))
        {
            //if todoItem already was changed, print details on changes
            if (isset($row["lastEditedOn"]))
            {
                //convert date from database from string into timestamp into DateTime object
                $date = new DateTime();
                $timestamp = strtotime($row["lastEditedOn"]);
                $date->setTimestamp($timestamp);

                //get username of editor
                $userQuery = "SELECT username FROM user WHERE userId = '" . $row['userIdOfLastEdit'] . "';";
                $username = Database::selectQuery($userQuery)->fetch_assoc()["username"];

                $todo = new ToDoListItem($row["todoId"], $row["userId"], $row["todoTitle"], $row["status"], $date, $row["todoText"], $username);
            }
            else
            {
                //new DateTime(01/01/1900) is kind of a default value -> see more infos in todoListItem class
                $todo = new ToDoListItem($row["todoId"], $row["userId"], $row["todoTitle"], $row["status"], new DateTime('01/01/1900'), $row["todoText"]);
            }
        }
        else
        {
            $todo = new ToDoListItem($row["todoId"], $row["userId"], $row["todoTitle"], $row["status"], new DateTime('01/01/1900'));
        }
        echo $todo;
    }
    echo "</div></div>";
}

//=========================================//
//               addTodo()                 //
//=========================================//

/**
 * This function adds a new todoListItem into the database
 */
function addTodo()
{
    if (isset($_REQUEST["newTaskInputText"]))
    {
        //if there is a todoText
        $insertQuery = "INSERT INTO todo (todoId, userId, status, todoTitle, todoText, createdOn) VALUES (NULL, '" . $_SESSION["user"]["userId"] . "', 1, '" . $_REQUEST["newTaskInputTitle"] . "', '" . $_REQUEST["newTaskInputText"] . "', current_timestamp());";
    }
    else
    {
        //if there is no todoText
        $insertQuery = "INSERT INTO todo (todoId, userId, status, todoTitle, createdOn) VALUES (NULL, '" . $_SESSION["user"]["userId"] . "', 1, '" . $_REQUEST["newTaskInputTitle"] . "', current_timestamp());";
    }
    Database::insertQuery($insertQuery);
}

//=========================================//
//            delete TodoItem              //
//=========================================//

/**
 * This function deletes an todoItem from database
 */
function deleteTask($todoId, $userId): bool
{
    $userQuery = "SELECT userId FROM todo WHERE todoId = '" . $todoId . "';";
    $todoOwner = Database::selectQuery($userQuery)->fetch_assoc()['userId'];
    if ($todoOwner == $userId || $_SESSION["user"]["role"] == 2)
    {
        $deleteQuery = "DELETE FROM todo WHERE todoId='" . $todoId . "';";
        if (Database::deleteQuery($deleteQuery))
        {
            return true;
        }
    }
    return false;
}

//=========================================//
//              change Status              //
//=========================================//

/**
 * This function changes the current status of an todoItem
 */
function changeStatus($todoId, $userId): bool
{
    $userQuery = "SELECT userId FROM todo WHERE todoId = '" . $todoId . "';";
    $todoOwner = Database::selectQuery($userQuery)->fetch_assoc()['userId'];
    if ($todoOwner == $userId || $_SESSION["user"]["role"] == 2)
    {
        $updateQuery = "UPDATE todo SET status=!(SELECT status FROM todo where todoId='" . $todoId . "') WHERE todoId ='" . $todoId . "';";
        if (Database::updateQuery($updateQuery))
        {
            return true;
        }
    }
    return false;
}

//=========================================//
//              updateTodo()               //
//=========================================//

/**
 * This items saves changes, which were made on a todoItem
 * @param string $id the id of the changed todoItem
 * @param string $title the (new) title of the changed todoItem
 * @param string $text the (new) text of the changed todoItem
 */
function editTodo(string $id, string $title, string $text = ""): void
{
    $userQuery = "SELECT userId FROM todo WHERE todoId = '" . $id . "';";
    $todoOwner = Database::selectQuery($userQuery)->fetch_assoc()['userId'];
    if ($todoOwner == $_SESSION["user"]["userId"] || $_SESSION["user"]["role"] == 2)
    {
        $queryTitle = "UPDATE todo SET todoTitle='" . $title . "' WHERE todoId ='" . $id . "';";
        $queryText = "UPDATE todo SET todoText='" . $text . "' WHERE todoId ='" . $id . "';";
        $queryDate = "UPDATE todo SET lastEditedOn = current_timestamp() WHERE todoId ='" . $id . "';";
        $queryUser = "UPDATE todo SET userIdOfLastEdit='" . $_SESSION["user"]["userId"] . "' WHERE todoId ='" . $id . "';";
        Database::updateQuery($queryTitle);
        Database::updateQuery($queryText);
        Database::updateQuery($queryDate);
        Database::updateQuery($queryUser);
    }
}

//=========================================//
//           userNameAvailable()           //
//=========================================//

/**
 * This functions checks, if an username is currently available for registration in the database
 * @param $username string the username which should be registered
 * @return bool true if username is available, else false
 */
function usernameAvailable(string $username): bool
{
    $selectQuery = "SELECT username FROM user WHERE username='" . $username . "';";
    $result = Database::selectQuery($selectQuery);
    if ($result->num_rows > 0)
    {
        return false;
    }
    return true;
}

?>
</body>
</html>

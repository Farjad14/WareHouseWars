<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta HTTP-EQUIV="EXPIRES" CONTENT="-1">
    <title>Computing Science 309 Warehouse Wars</title>
    <script language="javascript" src="jsFiles/jquery-3.1.1.min.js">
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <link rel="stylesheet" type="text/css" href="style/styles.css">
    <script language="javascript" src="jsFiles/ww.js">
    </script>
    <script language="javascript" src="userInfo.js">
    </script>
    <link rel="shortcut icon" type="image/png" href="icons/face-cool-24.png" />


    <script>
        var r = null;
        stage = null;
        // SOME GLUE CODE CONNECTING THIS PAGE TO THE STAGE
        interval = null;

        function displayTopThree() {
            $("#hiscores").hide();
            var request = $.ajax({
                url: "api/api.php",
                method: "POST",
                data: JSON.stringify({
                    usernameG: $("#usernameDisplay").val()
                }),
                contentType: "application/json; charset=UTF-8",

            });

            request.done(function(msg) {
                var returned = JSON.parse(msg);
                for (var x = 1; x <= returned.length; x++) {
                    var id = "s" + x;
                    document.getElementById(id).value = returned[x - 1].userscore;
                }

            });

            request.fail(function(jqXHR, textStatus) {
                console.log("not cool2? " + textStatus);
            });
        }

        function displayTopTen() {
            var request = $.ajax({
                url: "api/api.php",
                method: "POST",
                data: JSON.stringify({
                    type: "hs"
                }),
                contentType: "application/json; charset=UTF-8",

            });
            request.done(function(msg) {
                var returned = JSON.parse(msg);
                for (var x = 1; x <= returned.length; x++) {
                    var id = "t" + x;
                    document.getElementById(id).value = returned[x - 1].username + ": " + returned[x - 1].userscore;
                }

            });
        }

        function numGameUpdate() {
            var request = $.ajax({
                url: "api/api.php",
                method: "POST",
                data: JSON.stringify({
                    userNumG: $("#usernameDisplay").val(),
                    type: "updateNumG"
                }),
                contentType: "application/json; charset=UTF-8",

            });
            request.done(function(msg) {
                var returned = JSON.parse(msg);
                console.log(returned);

            });
        }

        function setupGame() {
            stage = new Stage(20, 20, "stage");
            stage.initialize();
        }

        function startGame() {
            // YOUR CODE GOES HERE
            numGameUpdate();
            interval = setInterval(function() {
                if (stage != null) {
                    stage.step();
                }
            }, 1000);
        }

        function pause() {
            stage.state = "pause";
            $("#status").html("GAME PAUSED");
            $("#btnPause").hide();
            $("#btnResume").show();
        }

        function resume() {
            stage.state = "play";
            $("#status").html("");
            $("#btnPause").show();
            $("#btnResume").hide();
        }

        function play() {
            setupGame();
            startGame();
            $("#score").html("Score: 0");
            $("#time").html("Time: 00:00:00");
            $("#btnPlay").hide();
	    $("#head").hide();
            $("#btnPause").show();
            //checkState();
        }

        function quit() {
            stage = null;
            $("#game").remove();
            $("#btnQuit").hide();
            $("#btnPlay").show();
            $("#score").html("");
            $("#time").html("");
            $("#status").html("");
	    $("#head").show();
	    
        }

        function endGame() {
            clearInterval(interval);
            interval = null;
            saveScore();
            //clearInterval(interval);
            //interval = null;
            $("#btnQuit").show();
            $("#btnPause").hide();
            $("#hiscores").hide();
            //$("#btnPlay").show();

            //$("#btnSave").show();




        }

        function saveScore() {

            var request = $.ajax({
                url: "api/api.php",
                method: "PUT",
                data: JSON.stringify({
                    usernameG: $("#usernameDisplay").val(),
                    scored: $("#scored").val(),
                    timed: $("#timeInSec").val()
                }),
                contentType: "application/json; charset=UTF-8",

            });

            request.done(function(msg) {
                console.log("score updated ");
                displayTopThree();
            });

            request.fail(function(jqXHR, textStatus) {
                console.log("score didn't update " + textStatus);
            });

        }

        function moveNorth() {
            stage.player.move(-1, 0);
        }

        function moveNorthEast() {
            stage.player.move(-1, 1);
        }

        function moveNorthWest() {
            stage.player.move(-1, -1);
        }

        function moveSouth() {
            stage.player.move(1, 0);
        }

        function moveSouthEast() {
            stage.player.move(1, 1);
        }

        function moveSouthWest() {
            stage.player.move(1, -1);
        }

        function moveWest() {
            stage.player.move(0, -1);
        }

        function moveEast() {
            stage.player.move(0, 1);
        }

        function checkKey(e) {

            e = e || window.event;
            //north
            if (stage != null) {
                if (stage.player) {
                    if (e.keyCode == '87') {
                        moveNorth();
                    } else if (e.keyCode == '69') {
                        // north_east
                        moveNorthEast();
                    } else if (e.keyCode == '81') {
                        // north_west
                        moveNorthWest();
                    } else if (e.keyCode == '88') {
                        // south
                        moveSouth();
                    } else if (e.keyCode == '67') {
                        // south east
                        moveSouthEast();
                    } else if (e.keyCode == '90') {
                        // south west
                        moveSouthWest();
                    } else if (e.keyCode == '65') {
                        // west
                        moveWest();
                    } else if (e.keyCode == '68') {
                        // east
                        moveEast();
                    }
                }
            }

            if (e.keyCode == '13') {
                // enter
                if ($("#loginDiv").is(":visible")) {
                    login();
                }
            }


        }
        // YOUR CODE GOES HERE

        $(function() {
            document.onkeydown = checkKey;
            $("#usernameDisplay").hide();
            $("#btnPause").hide();
            $("#btnResume").hide();
            $("#btnQuit").hide();
            displayTopTen();
        });
    </script>


</head>

<body>
    <center>
        <img id="head" src="icons/header.png" />
        <br>
        <br>
        <br>
        <tr><input type="button" id="logout" class="btn" value="Logout" style="display:none" /></tr>
        <tr><input type="button" id="profileDisplay" class="btn" value="Profile" style="display:none" /></tr>

        <table id="gameDisplay" style="display:none">
            <tr>
                <td>
                    <div id="stage"> </div>
                </td>
                <td>
                    <center>
                        <p id='time'></p>
                        <input type="text" id="timeInSec" value="0" style="display:none" />
                        <input type="text" id="scored" value="0" style="display:none" />
                        <p id='score'></p>
                        <p id='status'></p>
                        <h2>Legend</h2>
                        <table class="legend">
                            <tr>
                                <td> <img src="icons/blank.gif" id="blankImage" /> </td>
                                <td> <img src="icons/emblem-package-2-24.png" id="boxImage" /> </td>
                                <td> <img src="icons/face-cool-24.png" id="playerImage" /> </td>
                                <td> <img src="icons/face-devil-grin-24.png" id="monsterImage" /> </td>
                                <td> <img src="icons/elite.png" id="elite" /> </td>
                                <td> <img src="icons/wall.jpeg" id="wallImage" /> </td>
                            </tr>
                            <tr>
                                <td> Empty <br/> Square </td>
                                <td> Box </td>
                                <td> Player </td>
                                <td> Monster </td>
                                <td> Wall </td>
                            </tr>
                        </table>
                        <h2>Controls</h2>
                        <table class="controls">
                            <tr>
                                <td><img src="icons/north_west.svg" class="btn" onclick="moveNorthWest();" /></td>
                                <td><img src="icons/north.svg" class="btn" onclick="moveNorth();" /></td>
                                <td><img src="icons/north_east.svg" class="btn" onclick="moveNorthEast();" /></td>
                            </tr>
                            <tr>
                                <td><img src="icons/west.svg" class="btn" onclick="moveWest();" /></td>
                                <td>&nbsp;</td>
                                <td><img src="icons/east.svg" class="btn" onclick="moveEast();" /></td>
                            </tr>
                            <tr>
                                <td><img src="icons/south_west.svg" class="btn" onclick="moveSouthWest();" /></td>
                                <td><img src="icons/south.svg" class="btn" onclick="moveSouth();" /></td>
                                <td><img src="icons/south_east.svg" class="btn" onclick="moveSouthEast();" /></td>
                            </tr>
                        </table>
                        <br>
                        <br>
                        <input id="btnSave" type="button" class="btn-game" value="Save Score" style="display: None" />
                        <button id="btnPlay" type="submit" class="btn-game" onclick="play();">Start Game</button>
                        <button id="btnPause" type="submit" class="btn-game" onclick="pause();">Pause</button>
                        <button id="btnResume" type="submit" class="btn-game" onclick="resume();">Resume</button>
                        <button id="btnQuit" type="submit" class="btn-game" onclick="quit();">Save & Quit</button>


                        <table class="top3">
                            <p>Top 3 Highscore</p>
                            <tr>
                                <td><input type="text" id="s1" class="hs" value="" placeholder="Highest Score" readonly></input>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="text" id="s2" class="hs" value="" placeholder="2nd Highest Score" readonly></input>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="text" id="s3" class="hs" value="" placeholder="3rd Highest Score" readonly></input>
                                </td>
                            </tr>

                        </table>

                    </center>
                </td>
            </tr>
        </table>



        <tr><input type="text" id="usernameDisplay" value="" style="display:none"></tr>
        <tr><input type="text" id="operation" style="display:none"></tr>


        <div id="loginDiv">
            <input type="text" id="user" placeholder="Enter Username" autofocus required pattern="[A-Za-z0-9]+" title="Letters and Numbers Only"></input>
            </p>
            <input type="password" id="password" placeholder="Enter Password" required title="Enter a valid password"></input>
            </p>
            <p> <input type="button" onclick="login();" class="btn" value="Login" id="login" /></p>
            <br>
            <p>Not a member?</p>
            <input type="button" id="register" class="btn" value="Register" />

        </div>
        <table id="hiscores">
            <tr>
                <td>
                    <p>Top 10 High Scores</p>
                </td>
            </tr>
            <tr>
                <td><input type="text" id="t1" value="" class="hs" placeholder="High Score" readonly></input>
                </td>
            </tr>
            <tr>
                <td><input type="text" id="t2" value="" class="hs" placeholder="High Score" readonly></input>
                </td>
            </tr>
            <tr>
                <td><input type="text" id="t3" value="" class="hs" placeholder="High Score" readonly></input>
                </td>
            </tr>
            <tr>
                <td><input type="text" id="t4" value="" class="hs" placeholder="High Score" readonly></input>
                </td>
            </tr>
            <tr>
                <td><input type="text" id="t5" value="" class="hs" placeholder="High Score" readonly></input>
                </td>
            </tr>
            <tr>
                <td><input type="text" id="t6" value="" class="hs" placeholder="High Score" readonly></input>
                </td>
            </tr>
            <tr>
                <td><input type="text" id="t7" value="" class="hs" placeholder="High Score" readonly></input>
                </td>
            </tr>
            <tr>
                <td><input type="text" id="t8" value="" class="hs" placeholder="High Score" readonly></input>
                </td>
            </tr>
            <tr>
                <td><input type="text" id="t9" value="" class="hs" placeholder="High Score" readonly></input>
                </td>
            </tr>
            <tr>
                <td><input type="text" id="t10" value="" class="hs" placeholder="High Score" readonly></input>
                </td>
            </tr>

        </table>

        <div id="registerDiv" style="display:none">

                <p>Register</p>
		
                <p><input type="text" name="user" id="username" placeholder="Enter Username" required pattern="[A-Za-z0-9]+" title="Letters and Numbers Only" /></p>
                <p><input type="password" name="password" id="password2" placeholder="Enter Password" required/></p>
                <p><input type="text" name="name" id="name" placeholder="Enter Name" required pattern="[A-Za-z]+" title="Letters Only"/></p>
                <p><input type="email" name="email" id="email" placeholder="Enter Email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" title="Enter Valid Email"/></p>

                <p><input type="button" class="btn" id="signUp" value="Sign Up" /></p>
                <br>
                <p>Already registered?</p>
                <input type="button" class="btn" id="goBack" value="Go Back" />


        </div>

        <div id="profileDiv" style="display:none">

                <p>[Edit Profile]</p>
                <p> <input type="text" name="user3" id="user3" value="" placeholder="Enter Username" disabled/></p>
                <p> <input type="password" name="password" id="password3" placeholder="Enter Password" value="" required /></p>
                <p> <input type="text" name="name" id="name2" value="" placeholder="Enter Name" required pattern="[A-Za-z]+" title="Letters Only"/></p>
                <p> <input type="email" name="email" id="email2" value="" placeholder="Enter Email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" title="Enter Valid Email"/></p>

                <p> <input type="button" id="editProfile" class="btn" value="Make Changes" /></p>
                <br>
                <input type="button" id="goBackGame" class="btn" value="Go Back" />

   
        </div>


    </center>
    </td>
    </tr>
    </table>
    <table id="UserManage">
        <tr>
            <td>
                <center>



                </center>
            </td>
        </tr>
    </table>
    </center>
</body>

</html>

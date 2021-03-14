<!DOCTYPE html>
<html>
<head>
    <title>Racing Game</title>
    <style>
        #pongCanvas {
            border: 5px solid black;
            background-color: lightblue;
        }
    </style>
</head>

<body>

<canvas id="pongCanvas" width="480" height="320"></canvas>

<script>
//START VARIABLES
    //canvas is the game area
    var canvas = document.getElementById("pongCanvas");
    //ctx is the tool used for drawing
    var ctx = canvas.getContext("2d");

//PADDLE VARIABLES
    var paddleHeight = 10;
    var paddleWidth = 75;
    //starting point of paddle
    var paddleX = (canvas.width-paddleWidth) / 2;

//BALL VARIABLES
    //x and y start location of the ball
    var x = canvas.width/2;
    var y = canvas.height -30;
    //amount we add to x and y each interval to move the ball
    var dx = 2;
    var dy = -2;
    //radius of the ball
    var ballRadius = 10;
//BRICK VARIABLES
    //How many columns and rows will bricks have?
    var brickRowCount = 3;
    var brickColumnCount = 5;
    //size of bricks
    var brickWidth = 75;
    var brickHeight = 20;
    //space between bricks
    var brickPadding = 10;
    //space between bricks and sides
    var brickOffsetTop = 30;
    var brickOffsetLeft = 30;
    //create the bricks in an array
    var bricks = [];
    //loop "c" to create columns
    // c starts at 0 and 1 is added to it until it is less than the column variable
    for(var c = 0; c < brickColumnCount; c++) {
        bricks[c] = [];
        //loop "r" to create rows for each column
        // r starts at 0 and 1 is added until it is less than the row variable
        for(var r = 0; r < brickRowCount; r++) {
            //this adds c & r to the array so we can identify each brick
            //then we add the x and y coordinates of that brick
            //then we add whether the brick has been hit or not (status)
            bricks[c][r] = { x: 0, y: 0, status: 1 };
        }
    }
//SCORE VARIABLE
    var score = 0;
    var level = 1;
//CONTROL VARIABLES
    //buttons pressed
    var rightPressed = false;
    var leftPressed = false;
//DRAW FUNCTIONS
    //MASTER draw function
    function draw() {
        //clears previous frame drawing within certain area
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        //calls the drawball function
            drawBall();
            //check if ball is touching left/right then reverse
            //use ballRadius so it's not calculated at center of circle
            if(x + dx > canvas.width-ballRadius || x + dx < ballRadius) {
                dx = -dx;
            }
            //check if ball is touching top then reverse
            if(y + dy < ballRadius) {
                dy = -dy;
            }
            //check if ball is touching bottom or paddle
            else if (y + dy > canvas.height - ballRadius) {
                //if ball is on paddle then reverse
                //x of objects is the left side
                if (x > paddleX && x < paddleX + paddleWidth) {
                    dy = -dy;
                }
                //if ball is not on paddle then end game
                else {
                    alert("GAME OVER");
                    //reloads the webpage
                    document.location.reload();
                    //clears the interval to restart the game
                    clearInterval(interval);
                }
            }
            //add to every frame to move the ball
            x += dx;
            y += dy;
        //calls the drawpaddle function
            drawPaddle();
            //if buttons pressed move the paddle
            if(rightPressed) {
                paddleX += 7;
                //stop from going off screen
                if (paddleX + paddleWidth > canvas.width){
                    paddleX = canvas.width - paddleWidth;
                }
            }
            else if(leftPressed) {
                paddleX -=7;
                //stop from going off screen
                if (paddleX < 0){
                    paddleX = 0;
                }
            }
        //calls the drawbrick function
            drawBricks();
        //calls the collision function
            collisionDetection();
        //calls the score function
            drawScore();
        //LOOP the draw function (itself)
        requestAnimationFrame(draw);
    }
    //draws BALL one per interval
    function drawBall() {
        //start drawing
        ctx.beginPath();
        //draw circle x y radius start angle/end angle
        ctx.arc(x, y, ballRadius, 0, Math.PI*2);
        //color
        ctx.fillStyle = "#0095DD";
        //fill
        ctx.fill();
        //end drawing
        ctx.closePath();
    }
    //draws PADDLE one per interval
    function drawPaddle() {
        ctx.beginPath();
        //rectangle X/Y/Width/Height
        ctx.rect(paddleX, canvas.height-paddleHeight, paddleWidth, paddleHeight);
        ctx.fillStyle = "#0095DD";
        ctx.fill();
        ctx.closePath();
    }
    //draws all the BRICKS per interval
    function drawBricks() {
        for(var c = 0; c < brickColumnCount; c++) {
            for(var r = 0; r < brickRowCount; r++) {
                //determine if brick has been hit before drawing
                //1 means it hasn't and 0 means it has
                if(bricks[c][r].status == 1) {
                    //determine XY coordinates for each brick
                    var brickX = (c*(brickWidth+brickPadding))+brickOffsetLeft;
                    var brickY = (r*(brickHeight+brickPadding))+brickOffsetTop;
                    //place brick on xy coordinates
                    bricks[c][r].x = brickX;
                    bricks[c][r].y = brickY;
                    //draw the brick
                    ctx.beginPath();
                    ctx.rect(brickX, brickY, brickWidth, brickHeight);
                    ctx.fillStyle = "#0095DD";
                    ctx.fill();
                    ctx.closePath();
                }
            }
        }
    }
    //draws the SCORE per interval
    function drawScore() {
        ctx.font = "16px Arial";
        ctx.fillStyle = "#0095DD";
        //text and XY coordinates
        ctx.fillText("Level: " + level, 8, 20);    
    }
//CONTROL LISTENERS
    //When a key is pressed, fire the KeyDownHandler function
    document.addEventListener("keydown", keyDownHandler, false);
    //When a key is lifted, fire the KeyDownHandler function
    document.addEventListener("keyup", keyUpHandler, false);
    
    //Sense the keys pressed
    function keyDownHandler(e) {
        //some browsers store "right" some store "ArrowRight"
        if(e.key == "Right" || e.key == "ArrowRight") {
            //this is the variable we declared at the top
            rightPressed = true;
        }
        else if(e.key == "Left" || e.key == "ArrowLeft") {
            leftPressed = true;
        }
    }
    //Sense the keys lifted
    function keyUpHandler(e) {
        if(e.key == "Right" || e.key == "ArrowRight") {
            rightPressed = false;
        }
        else if(e.key == "Left" || e.key == "ArrowLeft") {
            leftPressed = false;
        }
    }

//DETECT brick collision & COMPLETION of level
    function collisionDetection() {
        //loop through each brick
        for(var c=0; c<brickColumnCount; c++) {
            for(var r=0; r<brickRowCount; r++) {
                //assign b to each brick at a time
                var b = bricks[c][r];
                //check if it has been hit or not yet
                if(b.status == 1) {
                    //check if the ball's center is touching that brick
                    if(x > b.x && x < b.x+brickWidth && y > b.y && y < b.y+brickHeight) {
                        //if it is then reverse
                        dy = -dy;
                        //if hit set status to 0 so it won't get drawn
                        b.status = 0;
                        //add 1 to score variable
                        score++;
                        //if all breaks are destroyed move to NEXT LEVEL
                        if(score == brickRowCount * brickColumnCount) {
                            alert("Congratulations! Now for the next level");
                            //reset brick status
                            for(var c = 0; c < brickColumnCount; c++) {
                                for(var r = 0; r < brickRowCount; r++) {
                                    bricks[c][r].status = 1;
                                }
                            }
                            //reset score & add level
                            score = 0;
                            level++;
                            //reset ball location
                            x = canvas.width/2;
                            y = canvas.height -30;
                            //reset ball direction
                            if (dx < 0) {
                                dx = -dx;
                            }
                            if (dy > 0) {
                                dy = -dy;
                            }
                            //make the ball faster
                            dx = dx + .5;
                            dy = dy - .5;
                        }
                    }
                }
            }
        }
    }
//LOOP
    // this is the loop; draw() repeats every 10 miliseconds
        //var interval = setInterval(draw, 10);
    //run the draw function
    draw();
</script>
</body>
</html>

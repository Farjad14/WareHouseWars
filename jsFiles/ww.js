// Stage
// Note: Yet another way to declare a class, using .prototype.

//temp
// Return a random number between the range of a min and max
Stage.prototype.getRandomInt = function(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

function Stage(width, height, stageElementID){
	this.actors=[]; // all actors on this stage (monsters, player, boxes, ...)
	this.player=null; // a special actor, the player
	this.state="play";
    this.score = 0;
    this.time = 0;

	// the logical width and height of the stage
	this.width=width;
	this.height=height;

	// the element containing the visual representation of the stage
	this.stageElementID=stageElementID;

	// take a look at the value of these to understand why we capture them this way
	// an alternative would be to use 'new Image()'
	this.blankImageSrc=document.getElementById('blankImage').src;
	this.monsterImageSrc=document.getElementById('monsterImage').src;
    this.eliteImageSrc=document.getElementById('elite').src;
	this.playerImageSrc=document.getElementById('playerImage').src;
	this.boxImageSrc=document.getElementById('boxImage').src;
	this.wallImageSrc=document.getElementById('wallImage').src;
	
	//Box percentage
	this.boxPC = 0.4;
	
	//Monster percentage
	this.monPC = 0.1;
}

// initialize an instance of the game
Stage.prototype.initialize=function(){
	// Create a table of blank images, give each image an ID so we can reference it later
	var s='<table id="game" border=1>';
	// YOUR CODE GOES HERE
		for(var i=0;i<this.height;i++){
			s+="<tr>";
			for(var j=0;j<this.width;j++){
				s+= "<td><img id='" + this.getStageId(i,j) + "' src='" + this.blankImageSrc + "' /></td>";
			}
			s+="</tr>";
		}
	s+="</table>";
	// Put it in the stageElementID (innerHTML)
	document.getElementById("stage").innerHTML = s;
	//

	// Add the player to the center of the stage
	this.player = new Player(this, Math.round((this.width/2)-1), Math.round((this.height/2)-1));
	this.addActor(this.player);

	// Add walls around the outside of the stage, so actors can't leave the stage
	//West Walls
	for(var i=0;i<this.height;i++){
		this.addActor(new Wall(this, i, 0));
	}
	//North Walls
	for(var i=0;i<this.width;i++){
		this.addActor(new Wall(this, 0, i));
	}
	//East Walls
	for(var i=0;i<this.height;i++){
		this.addActor(new Wall(this, i, this.width-1));
	}
	//South
	for(var i=0;i<this.width;i++){
		this.addActor(new Wall(this, this.height-1, i));
	}
	
	// Add some Boxes to the stage
	//PC% of the navigable map should be boxes
	var maxBox = Math.round((this.width-2)*(this.height-2)*(this.boxPC));
	var numBox=0;
	while(numBox<maxBox){
		var x = Math.floor(Math.random() * (this.width-2)) + 1; 
		var y = Math.floor(Math.random() * (this.height-2)) + 1;
		if (document.getElementById(this.getStageId(x,y)).src==this.blankImageSrc){
			this.addActor(new Box(this, x, y));
			numBox++;
		}
			
	}

	// Add in some Monsters
	//PC% of the navigable map should be monsters
	this.maxMons = Math.round((this.width-2)*(this.height-2)*(this.monPC));
    //Lets have one elite Monster
    var numElite = 0;
	this.numMons=0;
	while(this.numMons<this.maxMons){
		var x = Math.floor(Math.random() * (this.width-2)) + 1;
		var y = Math.floor(Math.random() * (this.height-2)) + 1;
		if (document.getElementById(this.getStageId(x,y)).src==this.blankImageSrc){
            if(numElite==0){
                this.addActor(new eliteMonster(this, x, y));
                numElite++;
            }
            else{
                this.addActor(new Monster(this, x, y));
            }
			this.numMons++;
		}
			
	}

}
// Return the ID of a particular image, useful so we don't have to continually reconstruct IDs
Stage.prototype.getStageId=function(x,y){ return "stage_"+x+"_"+y; }

Stage.prototype.addActor=function(actor){
	this.actors.push(actor);
}

Stage.prototype.removeActor=function(actor){
	// Lookup javascript array manipulation (indexOf and splice).
	this.setImage(actor.x, actor.y, this.blankImageSrc);
	this.actors.splice(this.actors.indexOf(actor), 1);
	/*if(actor instanceof Player){
		this.player = null;
	}*/
}

// Set the src of the image at stage location (x,y) to src
Stage.prototype.setImage=function(x, y, src){
	document.getElementById(this.getStageId(x,y)).src=src;
}

Stage.prototype.playerDeath=function(){
	this.removeActor(this.player);
	this.player=null;
	this.state="done";
}

// Take one step in the animation of the game.  
Stage.prototype.step=function(){
    //Keep the game running
	if(this.state=="play"){
		for(var i=0;i<this.actors.length;i++){
		// each actor takes a single step in the game
		this.actors[i].step();
	}
    this.score++;
    this.time++;
    document.getElementById("score").innerHTML = "Score: " + this.score;
    document.getElementById("scored").value = this.score;

    var date = new Date(null);
    date.setSeconds(this.time);
document.getElementById("timeInSec").value = date.setSeconds(this.time);

    var utc = date.toUTCString();
    document.getElementById("time").innerHTML = "Time: " + utc.substr(utc.indexOf(':') - 2, 8);
	}
    //Player loses if they die
    else if(this.state=="done"){
                    document.getElementById("status").innerHTML = "Game Over";
                    endGame();
    }
    //Player wins if no monsters remain
    else if(this.numMons==0){
        document.getElementById("status").innerHTML = "You win!";
        endGame();
    }
	
}

// return the first actor at coordinates (x,y) return null if there is no such actor
// there should be only one actor at (x,y)!
Stage.prototype.getActor=function(x, y){
	var result = null;
	for(var i=0;i<this.actors.length;i++){
		if(this.actors[i].x==x && this.actors[i].y==y){
			result = this.actors[i];
		}
	}
	
	return result;
}
// End Class Stage

//Wall

//setup walls
function Wall(stage, x ,y){
	this.stage = stage;
	this.stage.setImage(x,y,this.stage.wallImageSrc);
	this.x=x;
	this.y=y;
	this.type = 'wall';
}
//Nothing can move a wall
Wall.prototype.move = function(other, dx, dy){
	return false;
}

// Does nothing each tick
Wall.prototype.step = function(){
	return; 
}//End Class Wall

//Box

function Box(stage, x ,y){
	this.stage = stage;
	this.stage.setImage(x,y,this.stage.boxImageSrc);
	this.x=x;
	this.y=y;
	this.type = 'box';
}
// Does nothing each tick
Box.prototype.step = function(){
	return;
}

Box.prototype.move = function(x, y){
	var dx = this.x + x;
	var dy = this.y + y;
	
	var n = this.stage.getActor(dx,dy);
	
	if(n instanceof Box){
		if(n.move(x,y)){
			this.stage.removeActor(this);
			this.x = dx;
			this.y = dy;
			this.stage.addActor(this);
			this.stage.setImage(this.x, this.y, this.stage.boxImageSrc);
			return true;
		}
	}
	else if(n==null){
		this.stage.removeActor(this);
		this.x = dx;
		this.y = dy;
		this.stage.addActor(this);
		this.stage.setImage(this.x, this.y, this.stage.boxImageSrc);
		return true;
	}
	else{
		return false;
	}
	
}

//Monster
function Monster(stage, x ,y){
	this.stage = stage;
	this.stage.setImage(x,y,this.stage.monsterImageSrc);
	this.x=x;
	this.y=y;
	//Directions: N, S, E, W, NE, SE, NW, NE
	this.xMoves = [0, 0, 1, -1, 1, 1, -1, -1]
	this.yMoves = [1, -1, 0, 0, 1, -1, 1, -1]
	this.type = 'mons';
}

Monster.prototype.dead=function(){
    var count = 0;
    for(i=0;i<this.yMoves.length;i++){
        var n = this.stage.getActor(this.x + this.xMoves[i], this.y + this.yMoves[i]);
        if(n instanceof Wall || n instanceof Monster || n instanceof Box){
            count++
        }
    }
    
    return count==8;
}

Monster.prototype.step=function(){
    //Check if still alive
		if(this.stage.state=='play'){
            if(this.dead()){
                this.stage.removeActor(this);
                this.stage.numMons--;
                this.stage.score +=100;
            }
            //Move if a valid move found
        else{
		var move = 0;
			move = Math.floor(Math.random() * 8) + 0;
			var dx = this.x + this.xMoves[move];
			var dy = this.y + this.yMoves[move];
			var d = this.stage.getActor(dx,dy);
			if((dx<this.stage.width) && (dy<this.stage.height) && (dy>1) && (dx>1) && (!(d instanceof Wall)) && (!(d instanceof Box))){
				if(d instanceof Player){
                    //kill player
					this.stage.playerDeath();
				}
                //continue the move
					this.stage.removeActor(this);
					this.x = dx;
					this.x = dx;
					this.y = dy;
					this.stage.addActor(this);
					this.stage.setImage(dx,dy,this.stage.monsterImageSrc);
			}
        }
        }
}

//Monster
function eliteMonster(stage, x ,y){
	Monster.call(this, stage, x ,y);
    this.stage.setImage(x,y,this.stage.eliteImageSrc);
}
eliteMonster.prototype = Object.create(Monster.prototype);
eliteMonster.prototype.constructor = eliteMonster;

//Override step function
//Should continue to find a way to move unless dead
eliteMonster.prototype.step=function(){
    //Check if dead
		if(this.stage.state=='play'){
            if(this.dead()){
                this.stage.removeActor(this);
                this.stage.numMons--;
                this.stage.score +=500;
            }
        else{
          //Find an open spot and then move
		var cantMove = true;
		var move = 0;
        //Keep loooping until we can move to an open spot
		while(cantMove){
			move = Math.floor(Math.random() * 8) + 0;
			var dx = this.x + this.xMoves[move];
			var dy = this.y + this.yMoves[move];
			var d = this.stage.getActor(dx,dy);
			if((dx<this.stage.width) && (dy<this.stage.height) && (dy>1) && (dx>1) && (!(d instanceof Wall)) && (!(d instanceof Box))){
				if(d instanceof Player){
                    //Killed player
					this.stage.playerDeath();
				}
                //Continue the move
					this.stage.removeActor(this);
					this.x = dx;
					this.x = dx;
					this.y = dy;
					this.stage.addActor(this);
					this.stage.setImage(dx,dy,this.stage.eliteImageSrc);
					cantMove = false;
			}
		}
        }
        }
}

function Player(stage, x, y){
	this.stage = stage;
	this.stage.setImage(x,y,this.stage.playerImageSrc);
	this.x=x;
	this.y=y;
	this.type = 'play';
}

Player.prototype.step = function(){
	return;
}

Player.prototype.move=function(x, y){
	if(this.stage.state=="play"){
	var dx = this.x + x;
	var dy = this.y + y;
	
	var n = this.stage.getActor(dx,dy);
	
	if(n instanceof Box){
        //Chain move to move other boxes, and if its possible
		if(n.move(x,y)){
			this.stage.removeActor(this);
			this.x = dx;
			this.y = dy;
			this.stage.addActor(this);
			this.stage.setImage(this.x, this.y, this.stage.playerImageSrc);
		}
	}
	else if(n instanceof Monster){
		this.stage.playerDeath();
	}
	else if(n==null){
		this.stage.removeActor(this);
		this.x = dx;
		this.y = dy;
		this.stage.addActor(this);
		this.stage.setImage(this.x, this.y, this.stage.playerImageSrc);
	}
}
}

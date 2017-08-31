Functions Description
======================
[![Poggit-CI](https://poggit.pmmp.io/ci.shield/BoxOfDevs/Functions/Functions)](https://poggit.pmmp.io/ci/BoxOfDevs/Functions/Functions)
<br>
[<img src="https://discordapp.com/assets/fc0b01fe10a0b8c602fb0106d8189d9b.png" width="162" height= "55">](https://discord.gg/6RXsK7w)

Ever wanted to make your own commands? Well, now you can! Using the functions plugin a user can set a function, and then on running a certain command, that function will be run - Enjoy :D
<br>
<br>
Commands
=========
All the commands currently implemented into Functions:

    - /function create <name of the function>
        Create a function!

    - /function addcmd <function name> <command>
        Add a command to a function!

    - /function setcmd <function name> <id> <command> (Functions 1.2+)
        Set a command to a function by it's id (can be found using read)!

    - /function usage <function name> <usage> (Functions 1.2+)
        Set the usage of a function!

    - /function desc <function name> <description> (Funtions 1.2+)
        Set the description of a function!

    - /function read <function name>
        Show every command of the function (nothink executes no commands!)

    - /function resetcmd <function name> <command id>
        Reset a command (leave it to blank to be changed later)

    - /function removecmd <function name> <command id>
        Remove the command (remove the space used for the command)


Permissions
===========
All the permissions used by Functions:

    - func.command.function : Allows /function, Default: OP

Special replacements
===========
Those are what in the command will be replace by what :~)
- {sender} : Will be replaced with sender name
- {isop} : Boolean if the sender is OP.
- {usage} : Will be replaced with the command usage
- {desc}: Will be replaced with the command description 
- {x} : Will be replaced with sender x coordinate
- {y} : Will be replaced with sender y coordinate
- {z} : Will be replaced with sender z coordinate
- {level} : Will be replaced with sender level
- {args[0]} : Will be replaced with the first argument of the command
- {args[1]} : Will be replaced with the second argument of the command
- {args[2]} : Will be replaced with the third argument of the command
- {args[3]} : Will be replaced with the fourth argument of the command    

You can also enter:
- {op} to set the user OP just for the command then unop him if (he wasn't OP).TtTT
- {console} to execute the command as the console

Here comes the complex part: the if statements. (Functions 1.2+)     
If statements follows a simple syntax in the command (removed after execution):     
{if:&lt;conditon&gt;;then:&lt;action&gt;}   


### List of possible conditions:
- value=other value: Check if two values are equal.
- value!=other value: Check if two values are not equal.
- value>other value: Check if a value is more than an other. Only for numbers.
- value&lt;other value: Check if a value is less than an other. Only for numbers.

### List of possible actions:
- exec (or execute): Will execute the command (if cancelled before)
- !exec (or !execute): Will cancel the execution of the command
- asop: Will execute the command as an OP.
- asconsole: Will execute the command as console.
- as&lt;online player name&gt;: Will execute all new commands as another player.

### AND and OR
You can check multiple conditions using && (and) or || (or). DO NOT USE BOTH IN THE SAME CONDITION !

Combined with other replacements, you can do thousands of things. Here are a few example:
- {if:{isop}!=true;then:!exec}: Will execute the command only if the player is OP.
- {if:{level}!=lobby;then:!exec}: Will execute the command only the command if the player is in the world "lobby" (like /fly commands)
- {if:{y}<10&&{y}>1;then:asop}: Will execute the command as OP if the sender is beetween 1 and 10 of height.
- {if:{args[0]}=read||{args[0]}=listen;then:!exec}: Will not execute the command if the 1st argument is "read" or "listen".

... create your own conditions will help you to really make interactive commands.

Importing / Exporting Functions (Functions 1.2.1+)
========
What's even cooler with this plugin is the fact that you can easily share Functions with other servers/on your network with a simple really light file.     

### Basic import/export
So first, you need to have a created function on your server.      
Then, you need to run /function export &lt;function name&gt;    
If succefull, you would see a new file in the function folder called &lt;function name&gt;.func .       
This file basicly stores the function and is needed to import the function on other servers.    
So share this file and put it into the Functions folder of the wanted server.   
Then, on this server, run command /function import &lt;name of he file without the .func&gt; .   
And voila ! You have succefully imported a function to an another server.   

### Import/export with password
Sometimes, you wouldn't like your shared functions to be leaked, so Functions comes with a buildt in feature of encrypting functions with passwords.    
Passwords are encrypted using an sha512 encryption then converted to ANSII, then reencrypted into 1 number so there is NO way back to the original password.    

So how to do this:  
First of all, as you did before, you need to have the function you want to share on a server.       
Then, run command /function export &lt;function name&gt; &lt;password&gt; .     
You would see if functions successfully encrypted your password at the success message.    
Now, as we did before, share it into an another server and put it into the Functions plugin folder.     
And finally run command /function import &lt;name of he file without the .func&gt; &lt;password&gt; .   
If no password included for a function that need one, you will get a message telling you to enter the password.     
If the password entered is wrong (could not decrypt the function), you will also get an error message.      


Authors
========
All the BoxOfDevs members who contributed to Functions:

    - Ad5001
    
    - TheDragonRing
    
    - applqpak
    
    - Dog2puppy
    
    - remotevase
Read The License!!!
========
Please make sure you read the license, upon downloading any file of the software, you agree to all its terms. The license may be found [here.] (http://bit.ly/2jikqXv)

Functions Description
======================
Ever wants to make your own commands? Well, now you can! Using the functions plugin a user can set a function, and then on running a certain command, that function will be run - Enjoy :D

Commands
=========
All the commands currently implemented into Functions:

    - /function create <name of the function>
        Create a function!

    - /function addcmd <function name> <command>
        Add a command to a function!

    - /function setcmd <function name> <id> <command>
        Set a command to a function by it's id (can be found using read)!

    - /function usage <function name> <usage>
        Set the usage of a function!

    - /function desc <function name> <description>
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

Here comes the complex part: the if statements.     
If statements follows a simple syntax in the command (removed after execution):     
{if:&lt;conditon&gt;;then:&lt;action&gt;}   


## List of possible conditions:
- value=other value: Check if two values are equal.
- value!=other value: Check if two values are not equal.
- value>other value: Check if a value is more than an other. Only for numbers.
- value&lt;other value: Check if a value is less than an other. Only for numbers.

## List of possible actions:
- exec (or execute): Will execute the command (if cancelled before)
- !exec (or !execute): Will cancel the execution of the command
- asop: Will execute the command as an OP.
- asconsole: Will execute the command as console.
- as&lt;online player name&gt;: Will execute all new commands as another player.

## AND and OR
You can check multiple conditions using && (and) or || (or). DO NOT USE BOTH IN THE SAME CONDITION !

Combined with other replacements, you can do thousands of things. Here are a few example:
- {if:{isop}!=true;then:!exec}: Will execute the command only if the player is OP.
- {if:{level}!=lobby;then:!exec}: Will execute the command only the command if the player is in the world "lobby" (like /fly commands)
- {if:{y}<10&&{y}>1;then:asop}: Will execute the command as OP if the sender is beetween 1 and 10 of height.
- {if:{args[0]}=read||{args[0]}=listen;then:!exec}: Will not execute the command if the 1st argument is "read" or "listen".

... create your own conditions will help you to really make interactive commands.

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
Please make sure you read the license, upon downloading any file of the software, you agree to all its terms. The license may be found [here.] (https://raw.githubusercontent.com/BoxOfDevs/Functions/master/LICENSE)

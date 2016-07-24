# MGMT Administration System
## Generic admin solution for any Laravel project.

**<span style="color:red;">This repository is hosted on a private VCS management server, and is not intended for public distribution</span>**

MGMT is a general purpose solution for any Laravel project to easily have a fully customizable administration solution within a few minutes of simple setup.  The system is still in early development, so expect to see at least a few bugs in it's current state.

### Installation
To make MGMT in it's present *early development* state available to your application, there are a couple of steps to follow.  We start by editing your `composer.json` file:
1. Include the private managed VCS server in your list of repositories:
```
"repositories": [
        {
            "type": "vcs",
            "url": "git@52.5.160.236:dan/mgmt.git"
        }
    ],
```
2. Next, require the package in your array of required packages.  Composer will look first in *Packagist*, then when it doesn't find MGMT there, it will try the private VCS server:
```
"require": {
        "dan/mgmt": "master"
    },
```
Now perform a composer update by running `composer update` from your project's route directory.  Composer will download the package into the appropriate folder.

### Implementation
After installing, you have to implement MGMT into your application.  This is easily accomplished by making your models inherit from the MgmtModel class instead of Illuminate's default Model class.
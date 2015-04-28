# Roadmap #

Short todo list for upcoming versions.
Items and timing may change.

# Current status #
Current repository version is 1.0.2 + fixes.

Working on

  * Fixes for 1.0.3
  * [1.1] Allow an alternative class system (Jobs like in FFXIV)

Done

  * Forum
  * Fixes for 1.0.1
  * Fixes for 1.0.2
  * [1.1] Get rid of generated javascript to improve caching
  * [1.1] Use jQuery 2.0 and ditch older browsers (<IE9, <FF4, <OP13)
  * [1.1] Improve binding setup code
  * [1.1] Better portal integration (checking on an external, logged in user) ([Issue #48](https://code.google.com/p/ppx-raidplaner/issues/detail?id=#48))
  * [1.1] Semi-automatic binding configuration
  * [1.1] Batch create raids ([Issue #31](https://code.google.com/p/ppx-raidplaner/issues/detail?id=#31))
  * [1.1] ~~Time based raid creation ([Issue #31](https://code.google.com/p/ppx-raidplaner/issues/detail?id=#31))~~ _dropped because of too many side effects and redundancy to batch creation_
  * [1.1] Vacation settings / user settings
  * [1.1] Theme extensions (randomizer, disable logout)
  * [1.1] Creating a new forum post when creating a new raid


---


# Version 1.1 #

ETA: N/A
"Automation"

  * Get rid of generated javascript to improve caching
  * Improve binding setup code
  * Better portal integration (checking on an external, logged in user) ([Issue #48](https://code.google.com/p/ppx-raidplaner/issues/detail?id=#48))
  * Batch create raids ([Issue #31](https://code.google.com/p/ppx-raidplaner/issues/detail?id=#31))
  * ~~Time based raid creation ([Issue #31](https://code.google.com/p/ppx-raidplaner/issues/detail?id=#31))~~ _dropped because of too many side effects and redundancy to batch creation_
  * Vacation settings / user settings
  * Creating a new forum post when creating a new raid
  * "Ghost mode" to view secondary roles directly in the raid view
  * Allow an alternative class system (Jobs like in FFXIV)
  * API to allow read-only access for external applications ([Issue #33](https://code.google.com/p/ppx-raidplaner/issues/detail?id=#33), [Issue #49](https://code.google.com/p/ppx-raidplaner/issues/detail?id=#49))


---


# Version 1.2 #

ETA: N/A
"DKP and Events"

  * Events as "lightweight" raids for regular members ([Issue #34](https://code.google.com/p/ppx-raidplaner/issues/detail?id=#34))
  * An additional group for "pugs" ([Issue #46](https://code.google.com/p/ppx-raidplaner/issues/detail?id=#46))
  * Support for common loot distribution systems (DKP/NiKarma/MugKP)
  * Log for raidlead changes
  * Get rid of mcrypt requirement / db-backed session handling


---


# Later versions #

The following features can be described as "yeah, I would totally like to see those, but there are no concrete plans yet".<br>

<ul><li>More theme options<br>
</li><li>Moving raids<br>
</li><li>Replace as many images as possible with CSS3 (and dump older browsers like IE7/8 in the process)
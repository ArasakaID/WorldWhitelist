# Description
A plugin that allowed you to whitelist player per world

## How does this plugin work?
You can set whitelist per world with this plugin. Allowing player for joining the world by whitelisted from Admin

## Command
| Command | Description |
| --- | --- |
| /ww set `<worldName>` [on/off] | Set on/off whitelist of the world |
| /ww add `<worldName>` `<playerName>` | Add player name to the whitelist |
| /ww remove `<worldName>` `<playerName>` | Remove player name from the whitelist |
| /ww setmessage `<worldName>` `<message>` | Set custom whitelist message, you can coloring the message with '&' |
| /ww list `<worldName>` | See players has been whitelisted |
| /ww help | See all WorldWhitelist Commands |


## Default Config
```yaml
---
#The default whitelist message
# - {WORLD} -> will be replaced with the world name
default-whitelist-message: "§cWorld {WORLD} is whitelisted!"

#Op bypass world whitelist
#This allowing OP user to ignore whitelist checking
op-bypass: true
...

```

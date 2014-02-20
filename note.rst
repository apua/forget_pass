雜記
====

pw 不支援 NIS

改密碼用 SED 配 ``:`` 作 delimiter ::
    > sed -E 's:^(apua:)[^\:]*(.*)$:\1hash\2:'

利用 NetApp 的備份能力放程式碼和 log 備份等等...
master.passwd 若有誤, 可依靠上次的 master.passwd 和 chpass.log 找出復原策略
(中間有可能有使用者循正常途徑變更密碼)

master.passwd, backup and chpass.log should be root only

注意到若有還原, 高安全性需求的狀況下應要求使用者重新變更密碼, 以避免原密碼早已被盜用

check if changing password successfully ::
    > printf "user apua\npass qwer1234\nquit\n" | nc 0 110

config.inc.php 也放在同一資料夾

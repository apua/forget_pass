雜記
====

pw 不支援 NIS

改密碼用 ::
    > sed -E 's/^(jeanyao:)[^:]*(:.*)$/\1apua\2/'

利用 NetApp 的備份能力放程式碼和 log 備份等等...
master.passwd 若有誤, 可依靠上次的 master.passwd 和 chpass.log 找出復原策略
(中間有可能有使用者循正常途徑變更密碼)

注意到若有還原, 高安全性需求的狀況下應要求使用者重新變更密碼, 以避免原密碼早已被盜用

舊的 master.passwd 保持 root read-only

密碼是否成功變更之策方法 ::
    > sh -c 'echo -e "user apua\npass qwer1234\nquit\n" | nc 0 110'

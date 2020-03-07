Dim shell,StdOut,currentPath,volume,isOccupied,webAddress,webPort

isOccupied=1
webAddress = "127.0.0.1"
webPort = "8081"
currentPath = replace(createObject("Scripting.FileSystemObject").GetFolder(".").Path, "\", "/")
volume = left(currentPath, 3)

Set shell = WScript.CreateObject("WScript.Shell")
Set StdOut = shell.Exec("netstat -an").StdOut

Do Until StdOut.AtEndOfStream
strLine = StdOut.ReadLine
If InStr(strLine, webPort) > 0 And InStrRev(strLine, "LISTENING") > 0 Then
isOccupied=0
Exit Do
End If
Loop

If isOccupied Then
command = volume + "Applications/Scoop/apps/php73 " + currentPath + "/app/bin/console server:run " + webAddress + ":" + webPort
shell.run command, 0, true
End If
wscript.sleep 1000*2
shell.run "http://" & webAddress & ":" & webPort
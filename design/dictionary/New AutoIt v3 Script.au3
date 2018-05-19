$in1 = FileOpen("1.txt", 0 + 128)
$in2 = FileOpen("2.txt", 0 + 128)
$out = FileOpen("3.txt", 2 + 128)


While 1
    $line1 = FileReadLine($in1)
    $line2 = FileReadLine($in2)
    If @error = -1 Then ExitLoop
    if $line1 <> $line2 then FileWriteLine($out, $line1 & @TAB & $line2)
    ;if $line1 == $line2 then FileWriteLine($out, $line2)
WEnd

FileClose($out)
FileClose($in1)
FileClose($in2)
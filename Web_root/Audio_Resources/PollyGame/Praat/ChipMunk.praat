form ChipMunk
	text srcfile s.wav
	text destfile mods.wav
endform

Read from file... 'srcfile$'
Extract left channel
sound_one$ = selected$ ("Sound")

# Change gender (make High) of the file and write back:
# Change gender... 75.0 600.0 1.6 0.0 1.0 0.6
Change gender... 75.0 500.0 1.6 0.0 1.0 0.6
#Play

# Lengthen (overlap-add)... 75.0 600 3.0 
Lengthen (overlap-add)... 75.0 500 3.0
#Play

Write to WAV file... 'destfile$'

select all
Remove

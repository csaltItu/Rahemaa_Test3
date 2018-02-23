form BkMusic
	text srcfile s.wav
	text destfile mods.wav
endform
music$ = "m.wav"

Read from file... 'srcfile$'

Extract left channel
sound_one$ = selected$ ("Sound")
Resample... 64000 50
Shift times to... "start time" 5.0
sound_two$ = selected$ ("Sound")
duration = Get total duration
#Play

Read from file... 'music$'
Extract left channel
sound_three$ = selected$ ("Sound")
Resample... 64000 50
duration = duration+10.0
Extract part... 0 duration parabolic 1.0 no
Multiply... 0.7
sound_four$ = selected$ ("Sound")
#Play

select Sound 'sound_two$'
plus Sound 'sound_four$'

Combine to stereo
Convert to mono
#Play

Write to WAV file... 'destfile$'

select all
Remove
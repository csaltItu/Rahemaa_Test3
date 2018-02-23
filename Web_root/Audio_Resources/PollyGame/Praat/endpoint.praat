form endpoint
	text srcfile s.wav
	text destfile mods.wav
endform

Read from file... 'srcfile$'

#Extract left channel
sound_file$ = selected$("Sound")
string$ = "silent"

# delimit and mark silence intervals
To TextGrid (silences)... 50 0 -25 0.1 0.1 silent sounding

temp_label$ = Get label of interval... 1 1

if temp_label$ = string$
	Set interval text... 1 1 temp
endif

plus Sound 'sound_file$'

# extract voiced intervals into one Sound object per interval and concatenate
# these Sound object into one Sound object

Extract all intervals... 1 no
name$ = selected$ ("Sound")
bottom_name$ = selected$ ("Sound", -1)

numberOfSelectedSounds = numberOfSelected ("Sound") + 1

if bottom_name$ = string$
minus Sound 'bottom_name$'
endif

if name$ = "temp"
minus Sound 'name$'
endif

Concatenate

select all

for i from 2 to 'numberOfSelectedSounds'
sound'i' = selected ("Sound", -i)
endfor

for i from 2 to 'numberOfSelectedSounds'
	select sound'i'
	Remove
endfor

select Sound 'sound_file$'
plus TextGrid 'sound_file$'
	Remove

select Sound chain
Write to WAV file... 'destfile$'
#########################################################
## SET THESE PARAMETERS AS NECESSARY FOR YOUR COMPUTER ##
#########################################################
form autotune_polly
	text file_name s.wav
	text name
endform

# Directory where the files will be located...
#source_directory$ = "C:\Users\Nikolas Wolfe\Dropbox\Research\polly_west_africa\sandbox"
#source_directory$ = "/home/nwolfe/Dropbox/Research/polly_west_africa/sandbox"

# Name of the file to open and autotune...
#file_name$ = "polly_west_africa.wav"

# Forward slash or back slash: Change for Windows, Mac, Linux...
sep$ = "\\" 

# Suffix to append to new files. Make '' to simply overwrite... 
output_file$ = "ModifiedRecordings"+sep$+"4-"+name$ 

# Extension...
#extension$ = ".wav"

# Number of notes in the chromatic scale -- LEAVE THIS ALONE!
# Start ~ C 32 Hz
#scale_begin = 1
scale_begin = 13
# Stop ~ C 4.5 kHz 
#scale_size = 126
scale_size = 97

# Multiply factor for volume	
scale_peak = 4.0

# Adjust the time scale (1 = real time)	
scale_time = 1.0

# Maximum pitch difference for notes
threshold = 50

# Switches for various algorithms
algo_high_low = 1 
algo_closest = 0
algo_thresh = 0

# Manipulation settings
time_step = 0.0025
freq_low = 60
freq_high = 4000

# Switches for various scales
# Choices
# ---------------------------
# c_major
# c_minor
# c_whole_tone
# c_minor_pentatonic
# c_minor_blues
# c_major_pentatonic
# c_major_blues
# c_harmonic_minor
# db_diminished
# c_augmented
# chromatic
# c_hungarian_minor
# c_phrygian_dominant
# ---------------------------
#
music_scale$ = "c_minor_pentatonic"
#
#
#
#
#  LEAVE THE STUFF BELOW HERE ALONE, UNLESS YOU 
#  TOTALLY KNOW WHAT YOU'RE DOING. 
#
#
#
#########################################################
# Create scale-steps array...
chromatic_scale_size = 12
#########################################################
if music_scale$ = "c_minor"
	# C
	c_scale[1] = 1
	# C#/Db
	c_scale[2] = 0
	# D
	c_scale[3] = 1
	# D#/Eb
	c_scale[4] = 1
	# E
	c_scale[5] = 0
	# F
	c_scale[6] = 1
	# F#/Gb
	c_scale[7] = 0
	# G
	c_scale[8] = 1
	# G#/Ab
	c_scale[9] = 1
	# A
	c_scale[10] = 0
	# A#/Bb
	c_scale[11] = 1
	# B
	c_scale[12] = 0

endif
#########################################################
if music_scale$ = "c_minor_blues"
	# C
	c_scale[1] = 1
	# C#/Db
	c_scale[2] = 0
	# D
	c_scale[3] = 0
	# D#/Eb
	c_scale[4] = 1
	# E
	c_scale[5] = 0
	# F
	c_scale[6] = 1
	# F#/Gb
	c_scale[7] = 1
	# G
	c_scale[8] = 1
	# G#/Ab
	c_scale[9] = 0
	# A
	c_scale[10] = 0
	# A#/Bb
	c_scale[11] = 1
	# B
	c_scale[12] = 0

endif
#########################################################
if music_scale$ = "c_major_blues"
	# C
	c_scale[1] = 1
	# C#/Db
	c_scale[2] = 0
	# D
	c_scale[3] = 1
	# D#/Eb
	c_scale[4] = 1
	# E
	c_scale[5] = 1
	# F
	c_scale[6] = 0
	# F#/Gb
	c_scale[7] = 0
	# G
	c_scale[8] = 1
	# G#/Ab
	c_scale[9] = 0
	# A
	c_scale[10] = 1
	# A#/Bb
	c_scale[11] = 0
	# B
	c_scale[12] = 0

endif
#########################################################
if music_scale$ = "c_major_pentatonic"
	# C
	c_scale[1] = 1
	# C#/Db
	c_scale[2] = 0
	# D
	c_scale[3] = 1
	# D#/Eb
	c_scale[4] = 0
	# E
	c_scale[5] = 1
	# F
	c_scale[6] = 0
	# F#/Gb
	c_scale[7] = 0
	# G
	c_scale[8] = 1
	# G#/Ab
	c_scale[9] = 0
	# A
	c_scale[10] = 1
	# A#/Bb
	c_scale[11] = 0
	# B
	c_scale[12] = 0

endif
#########################################################
if music_scale$ = "c_phrygian_dominant"
	# C
	c_scale[1] = 1
	# C#/Db
	c_scale[2] = 1
	# D
	c_scale[3] = 0
	# D#/Eb
	c_scale[4] = 0
	# E
	c_scale[5] = 1
	# F
	c_scale[6] = 1
	# F#/Gb
	c_scale[7] = 0
	# G
	c_scale[8] = 1
	# G#/Ab
	c_scale[9] = 1
	# A
	c_scale[10] = 0
	# A#/Bb
	c_scale[11] = 1
	# B
	c_scale[12] = 1

endif
#########################################################
if music_scale$ = "c_hungarian_minor"
	# C
	c_scale[1] = 1
	# C#/Db
	c_scale[2] = 0
	# D
	c_scale[3] = 0
	# D#/Eb
	c_scale[4] = 1
	# E
	c_scale[5] = 1
	# F
	c_scale[6] = 1
	# F#/Gb
	c_scale[7] = 0
	# G
	c_scale[8] = 0
	# G#/Ab
	c_scale[9] = 1
	# A
	c_scale[10] = 1
	# A#/Bb
	c_scale[11] = 0
	# B
	c_scale[12] = 1

endif
#########################################################
if music_scale$ = "chromatic"
	# C
	c_scale[1] = 1
	# C#/Db
	c_scale[2] = 1
	# D
	c_scale[3] = 1
	# D#/Eb
	c_scale[4] = 1
	# E
	c_scale[5] = 1
	# F
	c_scale[6] = 1
	# F#/Gb
	c_scale[7] = 1
	# G
	c_scale[8] = 1
	# G#/Ab
	c_scale[9] = 1
	# A
	c_scale[10] = 1
	# A#/Bb
	c_scale[11] = 1
	# B
	c_scale[12] = 1

endif
#########################################################
if music_scale$ = "c_augmented"
	# C
	c_scale[1] = 1
	# C#/Db
	c_scale[2] = 0
	# D
	c_scale[3] = 0
	# D#/Eb
	c_scale[4] = 1
	# E
	c_scale[5] = 1
	# F
	c_scale[6] = 0
	# F#/Gb
	c_scale[7] = 0
	# G
	c_scale[8] = 1
	# G#/Ab
	c_scale[9] = 1
	# A
	c_scale[10] = 0
	# A#/Bb
	c_scale[11] = 0
	# B
	c_scale[12] = 1

endif
#########################################################
if music_scale$ = "db_diminished"
	# C
	c_scale[1] = 1
	# C#/Db
	c_scale[2] = 1
	# D
	c_scale[3] = 0
	# D#/Eb
	c_scale[4] = 1
	# E
	c_scale[5] = 1
	# F
	c_scale[6] = 0
	# F#/Gb
	c_scale[7] = 1
	# G
	c_scale[8] = 1
	# G#/Ab
	c_scale[9] = 0
	# A
	c_scale[10] = 1
	# A#/Bb
	c_scale[11] = 1
	# B
	c_scale[12] = 0

endif
#########################################################
if music_scale$ = "c_harmonic_minor"
	# C
	c_scale[1] = 1
	# C#/Db
	c_scale[2] = 0
	# D
	c_scale[3] = 1
	# D#/Eb
	c_scale[4] = 1
	# E
	c_scale[5] = 0
	# F
	c_scale[6] = 1
	# F#/Gb
	c_scale[7] = 0
	# G
	c_scale[8] = 1
	# G#/Ab
	c_scale[9] = 1
	# A
	c_scale[10] = 0
	# A#/Bb
	c_scale[11] = 0
	# B
	c_scale[12] = 1

endif
#########################################################
if music_scale$ = "c_minor_pentatonic"
	# C
	c_scale[1] = 1
	# C#
	c_scale[2] = 0
	# D
	c_scale[3] = 0
	# D#
	c_scale[4] = 1
	# E
	c_scale[5] = 0
	# F
	c_scale[6] = 1
	# F#
	c_scale[7] = 0
	# G
	c_scale[8] = 1
	# G#
	c_scale[9] = 0
	# A
	c_scale[10] = 0
	# A#
	c_scale[11] = 1
	# B
	c_scale[12] = 0

endif
#########################################################
if music_scale$ = "c_major"
	# C
	c_scale[1] = 1
	# C#
	c_scale[2] = 0
	# D
	c_scale[3] = 1
	# D#
	c_scale[4] = 0
	# E
	c_scale[5] = 1
	# F
	c_scale[6] = 1
	# F#
	c_scale[7] = 0
	# G
	c_scale[8] = 1
	# G#
	c_scale[9] = 0
	# A
	c_scale[10] = 1
	# A#
	c_scale[11] = 0
	# B
	c_scale[12] = 1

endif
#########################################################
if music_scale$ = "c_whole_tone"
	# C
	c_scale[1] = 1
	# C#
	c_scale[2] = 0
	# D
	c_scale[3] = 1
	# D#
	c_scale[4] = 0
	# E
	c_scale[5] = 1
	# F
	c_scale[6] = 0
	# F#
	c_scale[7] = 1
	# G
	c_scale[8] = 0
	# G#
	c_scale[9] = 1
	# A
	c_scale[10] = 0
	# A#
	c_scale[11] = 1
	# B
	c_scale[12] = 0

endif
#########################################################
#
# Modify the above to achieve a different scale, e.g. 
# Chromatic, Augmented, Harmonic Minor, Diminished, etc.
#
#########################################################
## Setup for AutoTune.... ###############################
#########################################################

#Creates an array with the chromatic scale, 126 notes in all
for j from 'scale_begin' to 'scale_size'
	#C = 16.352 hz
	if j = 1
	notes [j] = 16.352
	endif

	#C-Sharp = 17.324 hz
	if j = 2
	notes [j] = 17.324
	endif

	#D = 18.354 hz
	if j = 3
	notes [j] = 18.354
	endif

	#D-Sharp = 19.445 hz
	if j = 4
	notes [j] = 19.445
	endif

	#E = 20.602 hz
	if j = 5
	notes [j] = 20.602
	endif

	#F = 21.827 hz
	if j = 6
	notes [j] = 21.827
	endif

	#F-Sharp = 23.125 hz
	if j = 7
	notes [j] = 23.125
	endif

	#G = 24.500 hz
	if j = 8
	notes [j] = 24.500
	endif

	#G-Sharp = 25.957 hz
	if j = 9
	notes [j] = 25.957
	endif

	#A = 27.500 hz
	if j = 10
	notes [j] = 27.500
	endif

	#A-Sharp = 29.135 hz
	if j = 11
	notes [j] = 29.135
	endif

	#B = 30.868 hz
	if j = 12
	notes [j] = 30.868
	endif

	#C = 32.703 hz
	if j = 13
	notes [j] = 32.703
	endif

	#C-Sharp = 34.648 hz
	if j = 14
	notes [j] = 34.648
	endif

	#D = 36.708 hz
	if j = 15
	notes [j] = 36.708
	endif

	#D-Sharp = 38.891 hz
	if j = 16
	notes [j] = 38.891
	endif

	#E = 41.203 hz
	if j = 17
	notes [j] = 41.203
	endif

	#F = 43.654 hz
	if j = 18
	notes [j] = 43.654
	endif

	#F-Sharp = 46.249 hz
	if j = 19
	notes [j] = 46.249
	endif

	#G = 48.999 hz
	if j = 20
	notes [j] = 48.999
	endif

	#G-Sharp = 51.913 hz
	if j = 21
	notes [j] = 51.913
	endif

	#A = 55.000 hz
	if j = 22
	notes [j] = 55.000
	endif

	#A-Sharp = 58.270 hz
	if j = 23
	notes [j] = 58.270
	endif

	#B = 61.735 hz
	if j = 24
	notes [j] = 61.735
	endif

	#C = 65.406 hz
	if j = 25
	notes [j] = 65.406
	endif

	#C-Sharp = 69.296 hz
	if j = 26
	notes [j] = 69.296
	endif

	#D = 73.416 hz
	if j = 27
	notes [j] = 73.416
	endif

	#D-Sharp = 77.782 hz
	if j = 28
	notes [j] = 77.782
	endif

	#E = 82.407 hz
	if j = 29
	notes [j] = 82.407
	endif

	#F = 87.307 hz
	if j = 30
	notes [j] = 87.307
	endif

	#F-Sharp = 92.499 hz
	if j = 31
	notes [j] = 92.499
	endif

	#G = 97.999 hz
	if j = 32
	notes [j] = 97.999
	endif

	#G-Sharp = 103.826 hz
	if j = 33
	notes [j] = 103.826
	endif

	#A = 110.000 hz
	if j = 34
	notes [j] = 110.000
	endif

	#A-Sharp = 116.541 hz
	if j = 35
	notes [j] = 116.541
	endif

	#B = 123.471 hz
	if j = 36
	notes [j] = 123.471
	endif

	#C = 130.813 hz
	if j = 37
	notes [j] = 130.813
	endif

	#C-Sharp = 138.591 hz
	if j = 38
	notes [j] = 138.591
	endif

	#D = 146.832 hz
	if j = 39
	notes [j] = 146.832
	endif

	#D-Sharp = 155.563 hz
	if j = 40
	notes [j] = 155.563
	endif

	#E = 164.814 hz
	if j = 41
	notes [j] = 164.814
	endif

	#F = 174.614 hz
	if j = 42
	notes [j] = 174.614
	endif

	#F-Sharp = 184.997 hz
	if j = 43
	notes [j] = 184.997
	endif

	#G = 195.998 hz
	if j = 44
	notes [j] = 195.998
	endif

	#G-Sharp = 207.652 hz
	if j = 45
	notes [j] = 207.652
	endif

	#A = 220.000 hz
	if j = 46
	notes [j] = 220.000
	endif

	#A-Sharp = 233.082 hz
	if j = 47
	notes [j] = 233.082
	endif

	#B = 246.942 hz
	if j = 48
	notes [j] = 246.942
	endif

	#C = 261.626 hz
	if j = 49
	notes [j] = 261.626
	endif

	#C-Sharp = 277.183 hz
	if j = 50
	notes [j] = 277.183
	endif

	#D = 293.665 hz
	if j = 51
	notes [j] = 293.665
	endif

	#D-Sharp = 311.127 hz
	if j = 52
	notes [j] = 311.127
	endif

	#E = 329.628 hz
	if j = 53
	notes [j] = 329.628
	endif

	#F = 349.228 hz
	if j = 54
	notes [j] = 349.228
	endif

	#F-Sharp = 369.994 hz
	if j = 55
	notes [j] = 369.994
	endif

	#G = 391.995 hz
	if j = 56
	notes [j] = 391.995
	endif

	#G-Sharp = 415.305 hz
	if j = 57
	notes [j] = 415.305
	endif

	#A = 440.000 hz
	if j = 58
	notes [j] = 440.000
	endif

	#A-Sharp = 466.164 hz
	if j = 59
	notes [j] = 466.164
	endif

	#B = 493.883 hz
	if j = 60
	notes [j] = 493.883
	endif

	#C = 523.251 hz
	if j = 61
	notes [j] = 523.251
	endif

	#C-Sharp = 554.365 hz
	if j = 62
	notes [j] = 554.365
	endif

	#D = 587.330 hz
	if j = 63
	notes [j] = 587.330
	endif

	#D-Sharp = 622.254 hz
	if j = 64
	notes [j] = 622.254
	endif

	#E = 659.255 hz
	if j = 65
	notes [j] = 659.255
	endif

	#F = 698.456 hz
	if j = 66
	notes [j] = 698.456
	endif

	#F-Sharp = 739.989 hz
	if j = 67
	notes [j] = 739.989
	endif

	#G = 783.991 hz
	if j = 68
	notes [j] = 783.991
	endif

	#G-Sharp = 830.609 hz
	if j = 69
	notes [j] = 830.609
	endif

	#A = 880.000 hz
	if j = 70
	notes [j] = 880.000
	endif

	#A-Sharp = 932.328 hz
	if j = 71
	notes [j] = 932.328
	endif

	#B = 987.767 hz
	if j = 72
	notes [j] = 987.767
	endif

	#C = 1046.502 hz
	if j = 73
	notes [j] = 1046.502
	endif

	#C-Sharp = 1108.730 hz
	if j = 74
	notes [j] = 1108.730
	endif

	#D = 1174.659 hz
	if j = 75
	notes [j] = 1174.659
	endif

	#D-Sharp = 1244.508 hz
	if j = 76
	notes [j] = 1244.508
	endif

	#E = 1318.510 hz
	if j = 77
	notes [j] = 1318.510
	endif

	#F = 1396.913 hz
	if j = 78
	notes [j] = 1396.913
	endif

	#F-Sharp = 1479.978 hz
	if j = 79
	notes [j] = 1479.978
	endif

	#G = 1567.982 hz
	if j = 80
	notes [j] = 1567.982
	endif

	#G-Sharp = 1661.219 hz
	if j = 81
	notes [j] = 1661.219
	endif

	#A = 1760.000 hz
	if j = 82
	notes [j] = 1760.000
	endif

	#A-Sharp = 1864.655 hz
	if j = 83
	notes [j] = 1864.655
	endif

	#B = 1975.533 hz
	if j = 84
	notes [j] = 1975.533
	endif

	#C = 2093.004 hz
	if j = 85
	notes [j] = 2093.004
	endif

	#C-Sharp = 2217.461 hz
	if j = 86
	notes [j] = 2217.461
	endif

	#D = 2349.318 hz
	if j = 87
	notes [j] = 2349.318
	endif

	#D-Sharp = 2489.016 hz
	if j = 88
	notes [j] = 2489.016
	endif

	#E = 2637.020 hz
	if j = 89
	notes [j] = 2637.020
	endif

	#F = 2793.826 hz
	if j = 90
	notes [j] = 2793.826
	endif

	#F-Sharp = 2959.955 hz
	if j = 91
	notes [j] = 2959.955
	endif

	#G = 3135.963 hz
	if j = 92
	notes [j] = 3135.963
	endif

	#G-Sharp = 3322.438 hz
	if j = 93
	notes [j] = 3322.438
	endif

	#A = 3520.000 hz
	if j = 94
	notes [j] = 3520.000
	endif

	#A-Sharp = 3729.310 hz
	if j = 95
	notes [j] = 3729.310
	endif

	#B = 3951.066 hz
	if j = 96
	notes [j] = 3951.066
	endif

	#C = 4186.009 hz
	if j = 97
	notes [j] = 4186.009
	endif

	#C-Sharp = 4434.922 hz
	if j = 98
	notes [j] = 4434.922
	endif

	#D = 4698.636 hz
	if j = 99
	notes [j] = 4698.636
	endif

	#D-Sharp = 4978.032 hz
	if j = 100
	notes [j] = 4978.032
	endif

	#E = 5274.041 hz
	if j = 101
	notes [j] = 5274.041
	endif

	#F = 5587.652 hz
	if j = 102
	notes [j] = 5587.652
	endif

	#F-Sharp = 5919.911 hz
	if j = 103
	notes [j] = 5919.911
	endif

	#G = 6271.927 hz
	if j = 104
	notes [j] = 6271.927
	endif

	#G-Sharp = 6644.875 hz
	if j = 105
	notes [j] = 6644.875
	endif

	#A = 7040.000 hz
	if j = 106
	notes [j] = 7040.000
	endif

	#A-Sharp = 7458.620 hz
	if j = 107
	notes [j] = 7458.620
	endif

	#B = 7902.133 hz
	if j = 108
	notes [j] = 7902.133
	endif

	#C = 8372.018 hz
	if j = 109
	notes [j] = 8372.018
	endif

	#C-Sharp = 8869.844 hz
	if j = 110
	notes [j] = 8869.844
	endif

	#D = 9397.272 hz
	if j = 111
	notes [j] = 9397.272
	endif

	#D-Sharp = 9956.063 hz
	if j = 112
	notes [j] = 9956.063
	endif

	#E = 10548.082 hz
	if j = 113
	notes [j] = 10548.082
	endif

	#F = 11175.303 hz
	if j = 114
	notes [j] = 11175.303
	endif

	#F-Sharp = 11839.821 hz
	if j = 115
	notes [j] = 11839.821
	endif

	#G = 12543.854 hz
	if j = 116
	notes [j] = 12543.854
	endif

	#G-Sharp = 13289.750 hz
	if j = 117
	notes [j] = 13289.750
	endif

	#A = 14080.000 hz
	if j = 118
	notes [j] = 14080.000
	endif

	#A-Sharp = 14917.240 hz
	if j = 119
	notes [j] = 14917.240
	endif

	#B = 15804.266 hz
	if j = 120
	notes [j] = 15804.266
	endif

	#C = 16744.035 hz
	if j = 121
	notes [j] = 16744.035
	endif

	#C-Sharp = 17740.688 hz
	if j = 122
	notes [j] = 17739.688
	endif

	#D = 18794.544 hz
	if j = 123
	notes [j] = 18794.544
	endif

	#D-Sharp = 19912.126 hz
	if j = 124
	notes [j] = 19912.126
	endif

	#E = 21096.163 hz
	if j = 125
	notes [j] = 21096.163
	endif

	#F = 22350.606 hz
	if j = 126
	notes [j] = 22350.606
	endif
endfor

#########################################################
# Generate a scale array...
play_scale_size = 0
c_index = 0
for m from 'scale_begin' to 'scale_size'
	c_index = 'c_index' + 1
	if c_index > 'chromatic_scale_size'
		c_index = 1
	endif
	if c_scale ['c_index'] = 1
		play_scale_size = 'play_scale_size' + 1
		scalenotes ['play_scale_size'] = notes ['m']
	endif
endfor
#########################################################
#
# Okay, let's do this!
#
# Now that we have our possible notes, let's get to autotuning
#	
######################################################
## This is where the autotuning stuff should happen ##
######################################################
Read from file... 'file_name$'
utt$=selected$("Sound")

#These steps create Praat objects that allow us to modify pitch
select Sound 'utt$'
#To Manipulation... 0.01 16 22400
To Manipulation... 'time_step' 'freq_low' 'freq_high'
Extract pitch tier
#Get the number of points on the tier to correct
numPoints = Get number of points
 
#Extract time and pitch information from the points
for k from 1 to 'numPoints'
	pitches [k] = Get value at index... k
	times [k] = Get time from index... k
endfor
 
#Remove the original pitch information
Remove points between... 'times[1]' times ['numPoints']
 
#Add new pitch information
#For each point, we move the freq to the closest scale note
for q from 1 to 'numPoints'
	
	#The original freq of pitch
	currentfreq = pitches['q']
	
	#For loop finds the lowest difference between original pitch and a musical note
	noteindex = 1 
	scale_value = scalenotes ['noteindex']
	
	#########################################################
	## Thresholded -- Finds first note within hz threshold ##
	#########################################################
	if 'algo_thresh' = 1
		diff = 'threshold'
		for c from 1 to 'play_scale_size'
			scale_value = scalenotes ['c']
			diff2 = abs('currentfreq' - 'scale_value')
			if 'diff2' < 'diff'
				diff = 'diff2'
				noteindex = 'c'
			endif
		endfor
		scale_value = scalenotes ['noteindex']
	#########################################################
	else
		while currentfreq > 'scale_value'
			noteindex = min(('noteindex' + 1),'play_scale_size')
			#Finds next highest note
			scale_value = scalenotes ['noteindex']
		endwhile
		######################################################
		## Next Best -- Finds closest high or low notes ######
		######################################################
		if 'algo_high_low' = 1
			scale_value = scalenotes ['noteindex']
		elif 'algo_high_low' = 0
			scale_value = scalenotes [max(('noteindex' - 1),1)]
		endif
		######################################################
		## Closest note overall -- Checks high/low diff ######
		######################################################
		if 'algo_closest' = 1
			scale_value = scalenotes ['noteindex']
			prev_index = max(('noteindex' - 1),1)
			prev_scale_value = scalenotes ['prev_index']
			diff_curr = abs('currentfreq' - 'scale_value')
			diff_prev = abs('currentfreq' - 'prev_scale_value')
			if 'diff_prev' > 'diff_curr'
				scale_value = 'prev_scale_value'
			endif
		endif
		######################################################
	endif
	# Add point at the original time with the new pitch
	Add point... times['q'] scale_value
endfor
 
#Replace old pitch values with our new modified tier
select Manipulation 'utt$'
plus PitchTier 'utt$'
Replace pitch tier
 
#Generate a new autotuned Sound object
select Manipulation 'utt$'
Get resynthesis (LPC)

# Amplify and Scale
if 'scale_time' <> 1.0
	Scale times by... 'scale_time'
endif
if 'scale_peak' <> 1.0
	Scale peak... 'scale_peak'
endif

######################################################
## Ouput the autotuned files with a prefix ###########
######################################################

# Write to new wav file
#nowarn Write to WAV file... 'source_directory$''sep$''prefix$''utt$''extension$'
nowarn Write to WAV file... 'output_file$'
 
# Final clean up...
select all
Remove

source $HOME/.bash.conf/aliases.bash

export PS1="[\[\e[34m\]\A\[\e[m\]]\[\e[32m\]\h\[\e[m\]:\w \\$ "

GIT_PROMPT_ONLY_IN_REPO=1
GIT_PROMPT_SHOW_UNTRACKED_FILES=no
GIT_PROMPT_THEME=Single_line_Minimalist

source $HOME/.bash.conf/bash-git-prompt/gitprompt.sh
#source $HOME/.bash.conf/alternative.bash

source $HOME/.bash.conf/completion.bash



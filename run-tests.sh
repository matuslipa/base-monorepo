#/usr/bin/bash
set +e

VENDOR_BIN=vendor/bin

################################################################################
# Print success                                                                #
################################################################################
print_success() {
    echo -e "\e[32m$1\e[0m"
}

################################################################################
# Print warning                                                                #
################################################################################
print_info() {
    echo -e "\e[33m$1\e[0m"
}

################################################################################
# Print error                                                                  #
################################################################################
print_error() {
    echo -e "\e[31m$1\e[0m"
}

################################################################################
# Get git diff                                                                 #
################################################################################
git_diff() {
    local DIRECTORIES=$1

    BASE_BRANCH=$(
        git show-branch -a 2>/dev/null |
            grep '\*' |
            grep -v "$(git rev-parse --abbrev-ref HEAD)" |
            head -n1 | sed 's/.*\[\(.*\)\].*/\1/' |
            sed 's/[\^~].*//'
    )

    BASE_BRANCH=${BASE_BRANCH#"origin/"}

    echo "$(
        git diff --diff-filter=d --name-only "origin/$BASE_BRANCH" -- '*.php' |
            sed -e "s/^api\///" |
            egrep -i "^(($DIRECTORIES)|api\/($DIRECTORIES))" || echo "" |
            paste -sd " "
    )"
}

################################################################################
# Easy-coding-standard                                                         #
################################################################################
ecs() {
    local BIN_PATH=$VENDOR_BIN/ecs
    local FILES
    local FIX_OPTION
    local CACHE_OPTION

    if [[ ! -f $BIN_PATH ]]; then
        print_error "Easy-coding-standard executable not found in $BIN_PATH."
        return 1
    fi

    FIX_OPTION=$([[ $1 = true ]] && echo '--fix' || echo '')
    FILES=$([[ $2 = true ]] && echo "$(git_diff 'config|routes|App|database|resources/lang')" || echo '.')
    CACHE_OPTION=$([[ $3 = true ]] && echo '--clear-cache' || echo '')

    if [[ -n "$FILES" ]]; then
        php -d memory-limit=-1 $BIN_PATH check $FILES $CACHE_OPTION $FIX_OPTION
        return $? || 1
    else
        print_info "Easy-coding-standard skipped. No diff files to check available."
        echo
    fi

    return 0
}

################################################################################
# Rector                                                                       #
################################################################################
rector() {
    local BIN_PATH=$VENDOR_BIN/rector
    local FILES
    local FIX_OPTION
    local CACHE_OPTION

    if [[ ! -f $BIN_PATH ]]; then
        print_error "Rector executable not found in $BIN_PATH."
        return 1
    fi

    FIX_OPTION=$([[ $1 = true ]] && echo '' || echo '--dry-run')
    FILES=$([[ $2 = true ]] && echo "$(git_diff 'App')" || echo 'App')
    CACHE_OPTION=$([[ $3 = true ]] && echo '--clear-cache' || echo '')

    if [[ -n "$FILES" ]]; then
        php -d memory-limit=-1 $BIN_PATH process $FILES $CACHE_OPTION $FIX_OPTION
        return $? || 1
    else
        print_info "Rector skipped. No diff files to check available."
        echo
    fi

    return 0
}

################################################################################
# Phpstan                                                                      #
################################################################################
phpstan() {
    local BIN_PATH=$VENDOR_BIN/phpstan

    if [[ ! -f $BIN_PATH ]]; then
        print_error "Phpstan executable not found in $BIN_PATH."
        return 1
    fi

    if [[ $1 = true ]]; then
        php -d memory-limit=-1 $BIN_PATH clear-result-cache
    fi

    php -d memory-limit=-1 $BIN_PATH analyze
    return $? || 1
}

################################################################################
# Phpunit                                                                      #
################################################################################
phpunit() {
    local BIN_PATH=$VENDOR_BIN/phpunit

    if [[ ! -f $BIN_PATH ]]; then
        print_error "Phpunit executable not found in $BIN_PATH."
        return 1
    fi

    # run BIN_PATH as php script with no memory limit and pass all function arguments to it
    php -d memory-limit=-1 $BIN_PATH -c phpunit.xml "$@"
    return $? || 1
}

# Set default values for options
FIX=false
DIFF_ONLY=false
NO_CACHE=false
phpunit_arguments=""

# Define usage function
usage() {
    print_info "Usage: $0 <ecs|rector|phpstan|phpunit|code-style|all> [options] [arguments]"
    echo ""
    print_info "Options:"
    echo "  --diff-only   Only show diffs for ECS and Rector"
    echo "  --fix         Fix errors for ECS and Rector"
    echo "  --no-cache    Disable cache for ECS, Rector, and PHPStan"
    echo "  --help, -h    Display usage"
    echo ""
    print_info "Arguments:"
    echo "  Arguments for PHPUnit"
    echo ""
    exit 1
}

COMMAND=$1
shift

# Check for valid first argument
case $COMMAND in
"ecs" | "rector" | "code-style")
    while [[ "$#" -gt 0 ]]; do
        case $1 in
        "--diff-only")
            DIFF_ONLY=true
            shift
            ;;
        "--fix")
            FIX=true
            shift
            ;;
        "--no-cache")
            NO_CACHE=true
            shift
            ;;
        "--help" | "-h")
            usage
            ;;
        *)
            print_error "Invalid argument: $1"
            print_info "Use --help or -h for usage"
            exit 1
            ;;
        esac
    done
    ;;
"phpunit")
    phpunit_arguments="${*:1}"
    ;;
"phpstan")
    while [[ "$#" -gt 0 ]]; do
        case $1 in
        "--no-cache")
            NO_CACHE=true
            shift
            ;;
        "--help" | "-h")
            usage
            ;;
        *)
            print_error "Invalid argument: $1"
            print_info "Use --help or -h for usage"
            exit 1
            ;;
        esac
    done
    ;;
"all")
    while [[ "$#" -gt 0 ]]; do
        case $1 in
        "--diff-only")
            DIFF_ONLY=true
            shift
            ;;
        "--fix")
            FIX=true
            shift
            ;;
        "--no-cache")
            NO_CACHE=true
            shift
            ;;
        *)
            phpunit_arguments="${*}"
            break
            ;;
        esac
    done
    ;;
"--help" | "-h" | "")
    usage
    ;;
*)
    print_error "Invalid command: ${COMMAND}"
    print_info "Use --help or -h for usage"
    exit 1
    ;;
esac

RESULT=0
SUCCESS_MESSAGE="Ok."

# Print options for debugging
case $COMMAND in
"ecs" | "rector")
    ${COMMAND} $FIX $DIFF_ONLY $NO_CACHE || RESULT=1
    ;;
"code-style")
    ecs $FIX $DIFF_ONLY $NO_CACHE || RESULT=1
    rector $FIX $DIFF_ONLY $NO_CACHE || RESULT=1
    ;;
"phpunit")
    phpunit $phpunit_arguments || RESULT=1
    ;;
"phpstan")
    phpstan $NO_CACHE || RESULT=1
    ;;
"all")
    ecs $FIX $DIFF_ONLY $NO_CACHE || RESULT=1
    rector $FIX $DIFF_ONLY $NO_CACHE || RESULT=1
    phpstan $NO_CACHE || RESULT=1
    phpunit $phpunit_arguments || RESULT=1
    ;;
*) exit 1 ;;
esac

if [[ $RESULT -eq 0 ]]; then
    print_info "░░░░░░░█▐▓▓░████▄▄▄█▀▄▓▓▓▌█ Epic tests"
    print_info "░░░░░▄█▌▀▄▓▓▄▄▄▄▀▀▀▄▓▓▓▓▓▌█"
    print_info "░░░▄█▀▀▄▓█▓▓▓▓▓▓▓▓▓▓▓▓▀░▓▌█"
    print_info "░░█▀▄▓▓▓███▓▓▓███▓▓▓▄░░▄▓▐█▌ level is so high"
    print_info "░█▌▓▓▓▀▀▓▓▓▓███▓▓▓▓▓▓▓▄▀▓▓▐█"
    print_info "▐█▐██▐░▄▓▓▓▓▓▀▄░▀▓▓▓▓▓▓▓▓▓▌█▌"
    print_info "█▌███▓▓▓▓▓▓▓▓▐░░▄▓▓███▓▓▓▄▀▐█ much +code"
    print_info "█▐█▓▀░░▀▓▓▓▓▓▓▓▓▓██████▓▓▓▓▐█"
    print_info "▌▓▄▌▀░▀░▐▀█▄▓▓██████████▓▓▓▌█▌"
    print_info "▌▓▓▓▄▄▀▀▓▓▓▀▓▓▓▓▓▓▓▓█▓█▓█▓▓▌█▌ Wow."
    print_info "█▐▓▓▓▓▓▓▄▄▄▓▓▓▓▓▓█▓█▓█▓█▓▓▓▐█"
    print_success "$SUCCESS_MESSAGE"
else
    print_error "Well.. It seems that your code is not ready yet!"
fi

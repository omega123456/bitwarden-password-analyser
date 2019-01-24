$(function () {
    const checkFile = () => {
        $.get(
            '/check_file',
            (result) => {
                if (result === true) {
                    return window.location.reload();
                }

                setTimeout(checkFile, 2000);
            }
        );
    };

    if (shouldCheckFile === true) {
        setTimeout(checkFile, 2000);
    }
});

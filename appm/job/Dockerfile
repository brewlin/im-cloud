# brew/job
#
# @link     https://www.github.com/brewlin/im-cloud
# @license  https://github.com/brewlin/im-cloud/blob/master/LICENSE

FROM brewlin/im-cloud-base

ADD . /website/im-cloud

ENTRYPOINT ["php", "/website/im-cloud/appm/job/bin/app","--start","--log=true","--debug"]

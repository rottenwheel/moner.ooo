#!/bin/sh

path="$(realpath "$(dirname "$(command -v "$0")")")"

if test -z "$MT_PREFIX"; then
	MT_PREFIX="monerooo"
fi

unitpath=/usr/local/lib/systemd/system

mkdir -p "$unitpath"

for ex in coingecko haveno; do
	printf "\n====\ninstalling $unitpath/${MT_PREFIX}-$ex.service\n====\n" >&2;
	(
		printf "%s\n" \
			'[Unit]' \
			"Description=Run $ex ticker updater for moner.ooo" \
			'' \
			'[Service]' \
			'Type=oneshot' \
			'User=www-data' \
			'Group=www-data' \
			'Environment=MONEROOO_TICKER_UPDATE=1' \
			"ExecStart=php $path/public/$ex.php"
	) | tee /dev/stderr > "$unitpath/${MT_PREFIX}-$ex.service"

	printf "\n====\ninstalling $unitpath/${MT_PREFIX}-$ex.timer\n====\n" >&2;
	(
		printf "%s\n" \
			'[Unit]' \
			"Description=Periodic $ex ticker update for moner.ooo" \
			'' \
			'[Timer]' \
			'OnBootSec=5min' \
			'OnUnitActiveSec=5min' \
			'' \
			'[Install]' \
			'WantedBy=timers.target'
	) | tee /dev/stderr > "$unitpath/${MT_PREFIX}-$ex.timer"

	systemctl daemon-reload
	systemctl enable --now "${MT_PREFIX}-$ex.timer"
done

(function() {
  if (
    typeof omegaup === 'undefined' ||
    typeof omegaup.API === 'undefined' ||
    typeof omegaup.OmegaUp === 'undefined' ||
    typeof omegaup.UI === 'undefined'
  ) {
    // This should only be visible in development Virtual Machines.
    var statusElement = document.querySelector('#status');

    var statusMessageElement = statusElement.querySelector('.message');
    statusMessageElement.appendChild(document.createTextNode('Please run: '));

    var ttNode = document.createElement('tt');
    ttNode.appendChild(
      document.createTextNode(
        'cd /opt/omegaup && yarn install && yarn run dev',
      ),
    );
    statusMessageElement.appendChild(ttNode);

    statusElement.className = 'alert alert-danger';
    statusElement.style.display = 'block';
  }

  omegaup.OmegaUp.on('ready', function() {
    omegaup.API.Run.counts()
      .then(function(series) {
        {
          if (series.total.length == 0) return;

          var dataInSeries = [];
          var acInSeries = [];
          for (var i in series.total) {
            if (series.total.hasOwnProperty(i)) {
              dataInSeries.push(parseInt(series.total[i]));
            }
            if (series.ac.hasOwnProperty(i)) {
              acInSeries.push(parseInt(series.ac[i]));
            }
          }

          var minDate = new Date();
          minDate.setDate(minDate.getDate() - 30 * 3);

          var minY = dataInSeries[0] - dataInSeries[0] * 0.5;
          window.chart = new Highcharts.Chart({
            chart: {
              type: 'area',
              renderTo: 'runs-chart',
              height: 300,
              spacingTop: 20,
            },
            title: { text: 'Envíos totales' },
            xAxis: { type: 'datetime', title: { text: null } },
            yAxis: { title: { text: 'Envíos' }, min: minY },
            legend: { enabled: false },
            plotOptions: {
              area: {
                lineWidth: 1,
                marker: { enabled: false },
                shadow: false,
                states: { hover: { lineWidth: 1 } },
                threshold: null,
              },
            },
            series: [
              {
                type: 'area',
                name: 'Envíos',
                pointInterval: 24 * 3600 * 1000,
                pointStart: minDate.getTime(),
                data: dataInSeries.reverse(),
                fillColor: {
                  linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                  stops: [
                    [0, Highcharts.getOptions().colors[0]],
                    [
                      1,
                      Highcharts.Color(Highcharts.getOptions().colors[0])
                        .setOpacity(0)
                        .get('rgba'),
                    ],
                  ],
                },
              },
            ],
          });
        }
      })
      .fail(omegaup.UI.apiError);
  });

  omegaup.API.Contest.list({ active: 'ACTIVE' })
    .then(function(data) {
      var list = data.results;
      var now = new Date();

      var nextContestsList = document.getElementById('next-contests-list');
      for (var i = 0, len = list.length; i < len && i < 10; i++) {
        var link = document.createElement('a');
        link.href = '/arena/' + omegaup.UI.escape(list[i].alias) + '/';
        link.className = 'list-group-item';
        link.appendChild(document.createTextNode(list[i].title));
        nextContestsList.appendChild(link);
      }
    })
    .fail(omegaup.UI.apiError);
})();

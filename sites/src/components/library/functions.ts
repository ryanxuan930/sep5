export function getUrlParams(url: string, params:string|string[]) {
  try {
    var urlParams = new URL(url).searchParams;
  } catch (err) {
    return null;
  }
  if (Array.isArray(params)) {
    const data:(string|null)[] = [];
    params.forEach(param => { data.push(urlParams.get(param)) });
    return data;
  } else {
    console.log(url, urlParams.get(params))
    return urlParams.get(params);
  }
}

export function paginationText(page: string|number) {
  if (!isNaN(Number(page))) {
    return page;
  } else if (page === 'pagination.previous') {
    return '❮';
  } else {
    return '❯';
  }
}

export function csvToArray(str: string, delimiter = ',') {
  const headers = str.slice(0, str.indexOf('\r\n')).split(delimiter);
  const rows = str.slice(str.indexOf('\r\n') + 1).split(/\r\n|\n|\r/);
  rows.splice(0, 1);
  const arr = rows.map((row) => {
    const values = row.split(delimiter);
    const el = headers.reduce((object: any, header: string, index: number) => {
      object[header] = values[index];
      return object;
    }, {});
    return el;
  });
  return arr;
}

export function exportCsv(data: any[], title: string, header: any[]) {
  let temp = '';
  temp += `${header.join(',')}\r\n`;
  for (let i = 0; i < data.length; i++) {
    temp += `${data[i].join(',')}\r\n`;
  }
  const hiddenElement = document.createElement('a');
  hiddenElement.href = `data:text/csv;charset=utf-8,${encodeURI(temp)}`;
  hiddenElement.target = '_blank';
  hiddenElement.download = `${title}.csv`;
  hiddenElement.click();
}

export function openWindow(url: string) {
  window.open(url);
}
export function TemplateParser (template: string, data: Record<string, object> = {}) {
  return Object.entries(data).reduce((res, [key, value]) => {
      // lookbehind expression, only replaces if mustache was not preceded by a backslash
      const mainRe = new RegExp(`(?<!\\\\){{\\s*${key}\\s*}}`, 'g')
      // this regex is actually (?<!\\){{\s*<key>\s*}} but because of escaping it looks like that...
      const escapeRe = new RegExp(`\\\\({{\\s*${key}\\s*}})`, 'g')
      // the second regex now handles the cases that were skipped in the first case.
      return res.replace(mainRe, value.toString()).replace(escapeRe, '$1');
  }, template);
}
import console from "console";

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

// convert csv to array of arrays
export function csvToArrayTable(str: string, delimiter = ',', skipColumns = 0) {
  const rows = str.slice(str.indexOf('\r\n') + 1).split(/\r\n|\n|\r/);
  const arr = rows.map((row) => {
    const values = row.split(delimiter);
    return values;
  });
  arr.splice(0, skipColumns);
  return arr;
}

export function exportCsv(data: any[], title: string, header: any[]|null = null) {
  let temp = '';
  if (header != null) {
    temp += `${header.join(',')}\r\n`;
  }
  for (let i = 0; i < data.length; i++) {
    temp += `${data[i].join(',')}\r\n`;
  }
  const hiddenElement = document.createElement('a');
  hiddenElement.href = `data:text/csv;charset=utf-8,${encodeURIComponent(temp)}`;
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
      return res.replace(mainRe, String(value)).replace(escapeRe, '$1');
  }, template);
}

export function shuffle(array: any[]) {
  for (let i = array.length - 1; i > 0; i--) {
    let j = Math.floor(Math.random() * (i + 1));
    [array[i], array[j]] = [array[j], array[i]];
  }
}

// string format: 00:00:00 or 00:00.00 or 00.00 or 00:00.000 or 00.000
export function stringToMilliseconds(str: string) {
  const arr = String(str).split(':');
  return arr;
  let ms = 0;
  if (arr.length === 3) {
    ms += Number(arr[0]) * 3600000;
    ms += Number(arr[1]) * 60000;
    ms += Number(arr[2]) * 1000;
  } else if (arr.length === 2) {
    ms += Number(arr[0]) * 60000;
    ms += Number(arr[1]) * 1000;
  } else {
    ms += Number(arr[0]) * 1000;
  }
  return ms;
}

export function lanePhaseToString(phase: number, locale: string) {
  const ch = ['其他', '第一輪', '預賽', '準決賽', '決賽'];
  const en = ['Others', 'Round I', 'Preliminary', 'Semi-finals', 'Finals'];
  if (locale == 'zh-TW') {
    return ch[phase];
  } else {
    return en[phase];
  }
}

export function getTargetPhase(currentPhase: number, params: any) {
  switch(currentPhase) {
    case 1:
      return 0;
    case 2:
      if (params.r1 == 0) {
        return 0;
      } else {
        return 1;
      }
    case 3:
      if (params.r1 == 0 && params.r2 == 0) {
        return 0
      } else if (params.r1 == 1 && params.r2 == 0) {
        return 1
      } else if (params.r1 == 1 && params.r2 == 1) {
        return 2
      }
    case 4:
      if (params.r1 == 0 && params.r2 == 0 && params.r3 == 0) {
        return 0
      } else if (params.r1 == 1 && params.r2 == 0 && params.r3 == 0) {
        return 1
      } else if (params.r1 == 1 && params.r2 == 1 && params.r3 == 0) {
        return 2
      } else if (params.r1 == 1 && params.r2 == 1 && params.r3 == 1) {
        return 3
      }
    default:
      return 0;
  }
}
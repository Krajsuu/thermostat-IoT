/**
 * Wykres historii temperatury (SVG) — jedna implementacja dla Alpine (live) i spójna skala Y.
 */

function sanitizeId(s) {
    return String(s || 'c').replace(/[^a-zA-Z0-9_-]/g, '') || 'c';
}

function escapeXml(s) {
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function normalizeTempRange(temps) {
    let minT = Math.min(...temps);
    let maxT = Math.max(...temps);
    const span = maxT - minT;
    if (span < 1e-9) {
        minT -= 0.5;
        maxT += 0.5;
    } else if (span < 1) {
        const mid = (maxT + minT) / 2;
        minT = mid - 0.5;
        maxT = mid + 0.5;
    }
    return { minT, maxT };
}

export function buildThermioHistorySvg(points, height, uniqueId) {
    const uid = sanitizeId(uniqueId);
    const h = Number(height) || 110;

    if (!Array.isArray(points) || points.length < 2) {
        return '<div class="text-center text-sm text-white/45 py-8">Za mało danych do wykresu</div>';
    }

    const temps = points.map((p) => Number(p.temp));
    if (temps.some((t) => Number.isNaN(t))) {
        return '<div class="text-center text-sm text-white/45 py-8">Nieprawidłowe dane</div>';
    }

    const { minT, maxT } = normalizeTempRange(temps);
    const plotLeft = 54;
    // Zapas po prawej: etykiety osi X mają text-anchor="middle", żeby nie obcinało ostatniej godziny.
    const plotRight = 376;
    const svgWidth = 400;
    const bottomY = h - 22;
    const topY = 18;
    const labelY = h - 4;
    const count = points.length;

    const yForTemp = (t) => {
        const n = (t - minT) / (maxT - minT);
        return bottomY - n * (bottomY - topY);
    };

    let gridLines = '';
    const tickLabels = [];
    for (let i = 0; i <= 4; i++) {
        const frac = i / 4;
        const tempVal = minT + frac * (maxT - minT);
        const y = bottomY - frac * (bottomY - topY);
        gridLines += `<line x1="${plotLeft}" y1="${y}" x2="${plotRight}" y2="${y}" stroke="rgba(255,255,255,0.08)" stroke-dasharray="3 4"/>`;
        tickLabels.push({ y, t: tempVal });
    }

    let pathD = '';
    const circles = [];
    points.forEach((point, i) => {
        const x = plotLeft + ((plotRight - plotLeft) * i) / Math.max(count - 1, 1);
        const y = yForTemp(Number(point.temp));
        pathD += i === 0 ? `M${x} ${y}` : ` L${x} ${y}`;
        circles.push({ x, y, label: point.label });
    });

    let yTexts = '';
    [0, 2, 4].forEach((i) => {
        const { y, t } = tickLabels[i];
        yTexts += `<text x="48" y="${y + 4}" text-anchor="end" fill="rgba(255,255,255,0.5)" font-size="10">${t.toFixed(1)}</text>`;
    });

    const xAxisLine = `<line x1="${plotLeft}" y1="${bottomY}" x2="${plotRight}" y2="${bottomY}" stroke="rgba(255,255,255,0.2)" stroke-dasharray="3 5"/>`;
    const yAxisLine = `<line x1="${plotLeft}" y1="${topY}" x2="${plotLeft}" y2="${bottomY}" stroke="rgba(255,255,255,0.25)"/>`;
    const unitLabel = `<text x="6" y="12" fill="rgba(255,255,255,0.4)" font-size="9">°C</text>`;

    let xLabels = '';
    circles.forEach((c) => {
        xLabels += `<text x="${c.x}" y="${labelY}" text-anchor="middle" fill="rgba(255,255,255,0.45)" font-size="10">${escapeXml(String(c.label))}</text>`;
    });

    let circEls = '';
    circles.forEach((c) => {
        circEls += `<circle cx="${c.x}" cy="${c.y}" r="3" fill="#dbeafe"/>`;
    });

    return `<svg viewBox="0 0 ${svgWidth} ${h}" class="w-full" xmlns="http://www.w3.org/2000/svg"><defs><filter id="glow-${uid}"><feGaussianBlur stdDeviation="3.5" result="coloredBlur"/><feMerge><feMergeNode in="coloredBlur"/><feMergeNode in="SourceGraphic"/></feMerge></filter></defs>${gridLines}${xAxisLine}${yAxisLine}${yTexts}${unitLabel}<path d="${pathD}" fill="none" stroke="#7dd3fc" stroke-width="2" filter="url(#glow-${uid})"/>${circEls}${xLabels}</svg>`;
}

export function statMin(points) {
    if (!Array.isArray(points) || points.length === 0) {
        return '—';
    }
    const v = Math.min(...points.map((p) => Number(p.temp)));
    if (Number.isNaN(v)) {
        return '—';
    }
    return `${v.toFixed(1)}°C`;
}

export function statMax(points) {
    if (!Array.isArray(points) || points.length === 0) {
        return '—';
    }
    const v = Math.max(...points.map((p) => Number(p.temp)));
    if (Number.isNaN(v)) {
        return '—';
    }
    return `${v.toFixed(1)}°C`;
}

export function statAvg(points) {
    if (!Array.isArray(points) || points.length === 0) {
        return '—';
    }
    const nums = points.map((p) => Number(p.temp)).filter((n) => !Number.isNaN(n));
    if (nums.length === 0) {
        return '—';
    }
    const sum = nums.reduce((a, b) => a + b, 0);
    return `${(sum / nums.length).toFixed(1)}°C`;
}

window.ThermioChart = {
    svg: buildThermioHistorySvg,
    statMin,
    statMax,
    statAvg,
};

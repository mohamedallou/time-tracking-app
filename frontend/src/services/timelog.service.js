export async function fetchLogs(pageSize = 100, page = 1) {
    const res =  await fetch(
        `/api/timelog/?page=${page}&pageSize=${pageSize}`
    );

    return await res.json();
}

export async function updateLog(id, payload) {
    const res =  await fetch(
        `/api/timelog/${id}`,
        {
            method: 'PATCH',
            body: JSON.stringify(payload),
            headers: {
                'Content-Type': 'application/json'
            }
        }
    );

    return await res.json();
}

export async function createLog(payload) {
    const res =  await fetch(
        `/api/timelog`,
        {
            method: 'POST',
            body: JSON.stringify(payload),
            headers: {
                'Content-Type': 'application/json'
            }
        }
    );

    return await res.json();
}

export async function deleteLog(id) {
    const res =  await fetch(
        `/api/timelog/${id}`,
        {
            method: 'DELETE',
        }
    );

    return await res.json();
}

export async function fetchStats(
    from = null,
    to = null,
    pageSize = 1000,
    page = 1,
) {
    let url = `/api/timelog/statistics/?page=${page}&pageSize=${pageSize}`;
    if (from) {
        url += `&from=${from}`
    }

    if (to) {
        url += `&to=${to}`
    }

    const res =  await fetch(
        url
    );

    return await res.json();
}
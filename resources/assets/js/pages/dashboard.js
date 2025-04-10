document.addEventListener('DOMContentLoaded', function() {
    // تهيئة الرسم البياني للنمو
    const contentGrowthOptions = {
        series: [{
            name: window.translations['Articles'] || 'Articles',
            data: articlesData
        }, {
            name: window.translations['News'] || 'News',
            data: newsData
        }, {
            name: window.translations['Users'] || 'Users',
            data: usersData
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        colors: ['#696cff', '#03c3ec', '#71dd37'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: dates,
            labels: {
                style: {
                    colors: '#697a8d',
                    fontSize: '13px'
                },
                rotate: -45,
                rotateAlways: false
            },
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#697a8d',
                    fontSize: '13px'
                },
                formatter: function (value) {
                    return Math.floor(value);
                }
            },
            min: 0,
            tickAmount: 4
        },
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function (value) {
                    return value + " " + (window.translations['items'] || 'items');
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            labels: {
                colors: '#697a8d'
            }
        },
        grid: {
            show: true,
            borderColor: '#f0f0f0',
            strokeDashArray: 4,
            padding: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0
            }
        }
    };

    // إنشاء الرسم البياني
    if (document.querySelector("#growthChart")) {
        const growthChart = new ApexCharts(document.querySelector("#growthChart"), contentGrowthOptions);
        growthChart.render();
    }

    // تهيئة التأثيرات الحركية
    const cards = document.querySelectorAll('.card-stats');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });

    // تحديث حالة المستخدمين المتصلين
    function updateOnlineUsers() {
        fetch('/api/online-users')
            .then(response => response.json())
            .then(data => {
                const usersList = document.querySelector('.online-users-list');
                if (usersList && data.users && data.users.length > 0) {
                    usersList.innerHTML = data.users.map(user => `
                        <div class="online-user-item d-flex align-items-center">
                            <div class="avatar-wrapper">
                                <img src="${user.profile_photo_path || '/assets/img/avatars/1.png'}"
                                     alt="${user.name}"
                                     class="avatar">
                                <span class="avatar-status bg-success"></span>
                            </div>
                            <div class="user-info">
                                <h6 class="user-name">${user.name}</h6>
                                <small class="user-status-text">${window.translations['Online'] || 'Online'}</small>
                            </div>
                            <div class="user-actions">
                                <div class="dropdown">
                                    <button type="button" class="btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="javascript:void(0);">
                                            <i class="ti ti-message me-1"></i>
                                            ${window.translations['Send Message'] || 'Send Message'}
                                        </a>
                                        <a class="dropdown-item" href="javascript:void(0);">
                                            <i class="ti ti-user me-1"></i>
                                            ${window.translations['View Profile'] || 'View Profile'}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    usersList.innerHTML = `
                        <div class="text-center p-4">
                            <i class="ti ti-users text-muted mb-2" style="font-size: 2rem;"></i>
                            <p class="mb-0">${window.translations['No online users at the moment'] || 'No online users at the moment'}</p>
                        </div>
                    `;
                }
            })
            .catch(console.error);
    }

    // تحديث كل دقيقة
    setInterval(updateOnlineUsers, 60000);
    
    // تحديث عند تحميل الصفحة
    updateOnlineUsers();

    // تحديث الإحصائيات بشكل دوري
    function updateStats() {
        fetch('/api/dashboard/stats')
            .then(response => response.json())
            .then(data => {
                document.getElementById('newsCount').textContent = data.newsCount;
                document.getElementById('articlesCount').textContent = data.articlesCount;
                document.getElementById('usersCount').textContent = data.usersCount;
            })
            .catch(console.error);
    }

    // تحديث كل 5 دقائق
    setInterval(updateStats, 300000);
});
